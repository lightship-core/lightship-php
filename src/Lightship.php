<?php

namespace Khalyomede;

use Closure;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Webmozart\Assert\Assert;

class Lightship
{
    /**
     * @var array<Domain>
     */
    private array $domains;

    /**
     * @var array<Route>
     */
    private array $routes;

    /**
     * @var array<Report>
     */
    private array $reports;
    private Closure $onReportedPageCallback;

    private Client $client;

    public function __construct(?Client $client = null)
    {
        $this->domains = [];
        $this->routes = [];
        $this->reports = [];
        $this->onReportedPageCallback = function (): void {
        };
        $this->client = $client instanceof Client ? $client : self::getHttpClient();
    }

    public static function getHttpClient(): Client
    {
        return new Client([
            RequestOptions::ALLOW_REDIRECTS => [
                "track_redirects" => true,
            ],
        ]);
    }

    /**
     * @todo Validate URL.
     */
    public function domain(string $domain): self
    {
        $d = new Domain();

        $d->setBase($domain);

        $this->domains[] = $d;

        return $this;
    }

    /**
     * @param array<string, string> $queries
     *
     * @todo Raise if page already added.
     */
    public function route(string $path, array $queries = []): self
    {
        $route = [
            "path" => $path,
            "queries" => $queries,
        ];

        if (empty($this->domains) || !str_starts_with("http", $path)) {
            $r = new Route();

            $q = array_map(fn (string $key, string $value): array => [
                "key" => $key,
                "value" => $value
            ], array_keys($queries), $queries);

            $r->setPath($path)
                ->setQueries($q);

            $this->routes[] = $r;
        } else {
            $this->domains[count($this->domains) - 1]->addRoute($route);
        }

        return $this;
    }

    public function analyse(): void
    {
        $analyser = new Analyser($this->client);

        foreach ($this->domains as $domain) {
            foreach ($domain->routes() as $route) {
                $page = new Page();

                $page->setDomain($domain->base())
                    ->setQueries($route->queries())
                    ->setPath($route->path());

                $report = $analyser->analyse($page);

                $this->reports[] = $report;

                call_user_func($this->onReportedPageCallback, [$page, $report]);
            }
        }

        foreach ($this->routes as $route) {
            $page = new Page();

            $q = [];

            foreach ($route->queries() as $query) {
                $q[$query->key()] = $query->value();
            }

            $page->setPath($route->path())
                ->setQueries($q);

            $report = $analyser->analyse($page);

            $this->reports[] = $report;

            call_user_func($this->onReportedPageCallback, [$page, $report]);
        }
    }

    public function onReportedPage(Closure $callback): self
    {
        $this->onReportedPageCallback = $callback;

        return $this;
    }

    public function toJson(): string
    {
        $json = json_encode($this->toArray());

        if (!is_string($json)) {
            throw new Exception(json_last_error_msg());
        }

        return $json;
    }

    public function toArray(): array
    {
        return array_map(fn (Report $report): array => $report->toArray(), $this->reports);
    }

    public function toPrettyJson(): string
    {
        $json = json_encode($this->toArray(), JSON_PRETTY_PRINT);

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

                $this->domains[] = (new Domain())
                    ->setRoutes($domain["routes"])
                    ->setBase($domain["base"]);
            }
        }

        if (isset($config["routes"])) {
            foreach ($config["routes"] as $route) {
                Assert::keyExists($route, "path");
                Assert::string($route["path"]);
                Assert::notEmpty($route["path"]);

                if (isset($route["queries"])) {
                    Assert::isArray($route["queries"]);
                }

                $this->routes[] = (new Route())
                    ->setPath($route["path"])
                    ->setQueries($route["queries"] ?? []);
            }
        }

        return $this;
    }
}
