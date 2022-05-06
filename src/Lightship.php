<?php

namespace Lightship;

use Closure;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Lightship\Exceptions\RuleNotFoundException;
use Lightship\Exceptions\UrlNotFoundException;
use Webmozart\Assert\Assert;

class Lightship
{
    protected string $domain;

    /**
     * @var array<Route>
     */
    protected array $routes;

    /**
     * @var array<Report>
     */
    protected array $reports;
    protected Closure $onReportedRouteCallback;

    protected Client $client;

    public function __construct(?Client $client = null)
    {
        $this->domain = "";
        $this->routes = [];
        $this->reports = [];
        $this->onReportedRouteCallback = function (): void {
        };
        $this->client = $client instanceof Client ? $client : self::getHttpClient();
    }

    /**
     * @todo Validate URL.
     */
    public function domain(string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @param array<string, string> $queries
     *
     * @todo Raise if route already added.
     */
    public function route(string $path, array $queries = []): self
    {
        if (empty($this->domain) || str_starts_with($path, "http")) {
            $r = new Route();

            $q = array_map(
                fn (string $key, string $value): Query => (new Query())
                    ->setKey($key)
                    ->setValue($value),
                array_keys($queries),
                $queries
            );

            $r->setPath($path)
                ->setQueries($q);

            $this->routes[] = $r;
        } else {
            /**
             * @var array<Query>
             */
            $q = array_map(
                fn (string $key, string $value): Query => (new Query())
                    ->setKey($key)
                    ->setValue($value),
                array_keys($queries),
                $queries
            );

            $this->routes[] = (new Route())
                ->setPath($this->domain . (str_ends_with($this->domain, "/") ? "" : "/") . ltrim($path, "/"))
                ->setQueries($q);
        }

        return $this;
    }

    public function analyse(): static
    {
        $analyser = new Analyser($this->client);

        foreach ($this->routes as $route) {
            $report = $analyser->analyse($route);

            $this->reports[] = $report;

            call_user_func($this->onReportedRouteCallback, $route, $report);
        }

        return $this;
    }

    public function onReportedRoute(Closure $callback): self
    {
        $this->onReportedRouteCallback = $callback;

        return $this;
    }

    public function toJson(): string
    {
        $json = json_encode($this->toArray()) . "\n";

        if (!is_string($json)) {
            throw new Exception(json_last_error_msg());
        }

        return $json;
    }

    /**
     * @return array<array{url: string, durationInSeconds: float, scores: array{seo: int, security: int, performance: int, accessibility: int}, seo: array<array{name: string, passes: bool}>, security: array<array{name: string, passes: bool}>, performance: array<array{name: string, passes: bool}>, accessibility: array<array{name: string, passes: bool}>}>
     */
    public function toArray(): array
    {
        return array_map(fn (Report $report): array => $report->toArray(), $this->reports);
    }

    public function toPrettyJson(): string
    {
        $json = json_encode($this->toArray(), JSON_PRETTY_PRINT) . "\n";

        if (!is_string($json)) {
            throw new Exception(json_last_error_msg());
        }

        return $json;
    }

    public function config(string $path): self
    {
        if (!file_exists($path) || !is_file($path)) {
            throw new Exception("File does not exist");
        }

        $content = file_get_contents($path);

        if (!is_string($content)) {
            throw new Exception("Cannot read file content");
        }

        $config = json_decode($content, true);

        if (!is_array($config)) {
            throw new Exception("Could not read config file (" . json_last_error_msg() . ")");
        }

        if (isset($config["domains"])) {
            Assert::isArray($config["domains"]);

            foreach ($config["domains"] as $domain) {
                Assert::keyExists($domain, "base");
                Assert::keyExists($domain, "routes");
                Assert::string($domain["base"]);
                Assert::notEmpty($domain["base"]);
                Assert::isArray($domain["routes"]);

                $base = $domain["base"];

                foreach ($domain["routes"] as $route) {
                    Assert::keyExists($route, "path");
                    Assert::string($route["path"]);
                    Assert::notEmpty($route["path"]);

                    $queries = array_map(
                        fn (array $query): Query => (new Query())
                            ->setKey($query["key"] ?? "")
                            ->setValue($query["value"] ?? ""),
                        $route["queries"] ?? []
                    );

                    $this->routes[] = (new Route())
                        ->setPath($base . (str_ends_with($base, "/") ? "" : "/") . ltrim($route["path"], "/"))
                        ->setQueries($queries);
                }
            }
        }

        if (isset($config["routes"])) {
            foreach ($config["routes"] as $route) {
                Assert::keyExists($route, "path");
                Assert::string($route["path"]);
                Assert::notEmpty($route["path"]);

                $queries = array_map(
                    fn (array $query): Query => (new Query())
                        ->setKey($query["key"] ?? "")
                        ->setValue($query["value"] ?? ""),
                    $route["queries"] ?? []
                );

                $this->routes[] = (new Route())
                    ->setPath($route["path"])
                    ->setQueries($queries);
            }
        }

        return $this;
    }

    public function client(Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @param array<string> $urls
     */
    public function allRulesPassed(array $urls): bool
    {
        $this->raiseIfUrlsNotFoundInReports($urls);

        $reports = $this->reportsMatchingUrls($urls);

        return count(array_filter($reports, fn (Report $report): bool => !$report->allRulesPassed())) === 0;
    }

    /**
     * @param array<string> $urls
     */
    public function rulePassed(array $urls, string $rule): bool
    {
        $this->raiseIfRulesDoNotExist([$rule]);
        $this->raiseIfUrlsNotFoundInReports($urls);

        $ruleClass = new $rule();

        assert($ruleClass instanceof Rule);

        $reports = $this->reportsMatchingUrls($urls);

        return count(array_filter($reports, fn (Report $report): bool => !$report->rulePassed($ruleClass))) === 0;
    }

    /**
     * @param array<string> $urls
     * @param array<string> $rules
     */
    public function someRulesPassed(array $urls, array $rules): bool
    {
        $this->raiseIfRulesDoNotExist($rules);
        $this->raiseIfUrlsNotFoundInReports($urls);

        return count(array_filter($rules, fn (string $rule): bool => !$this->rulePassed($urls, $rule))) === 0;
    }

    protected static function getHttpClient(): Client
    {
        return new Client([
            RequestOptions::ALLOW_REDIRECTS => [
                "track_redirects" => true,
            ],
            'version' => '2.0',
            RequestOptions::HEADERS => [
                "Accept-Encoding" => "gzip,deflate,br",
            ],
        ]);
    }

    /**
     * @param array<string> $urls
     *
     * @return array<Report>
     */
    protected function reportsMatchingUrls(array $urls): array
    {
        return array_filter($this->reports, function (Report $report) use ($urls): bool {
            return in_array($report->url, $urls, true);
        });
    }

    /**
     * @param array<string> $urls
     */
    protected function raiseIfUrlsNotFoundInReports(array $urls): void
    {
        $reportUrls = array_map(fn (Report $report): string => $report->url, $this->reports);
        $urlsNotFound = array_filter($urls, fn (string $url): bool => !in_array($url, $reportUrls, true));

        if (count($urlsNotFound) > 0) {
            $firstUrlNotFound = $urlsNotFound[0];

            throw new UrlNotFoundException("URL $firstUrlNotFound did not matched any report.");
        }
    }

    /**
     * @param array<string> $rules
     */
    protected function raiseIfRulesDoNotExist(array $rules): void
    {
        foreach ($rules as $rule) {
            if (!class_exists($rule)) {
                throw new RuleNotFoundException("Rule $rule not found.");
            }

            $ruleClass = new $rule();

            if (!($ruleClass instanceof Rule)) {
                throw new RuleNotFoundException("Rule $rule not found.");
            }
        }
    }
}
