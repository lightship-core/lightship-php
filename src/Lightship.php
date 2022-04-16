<?php

namespace Khalyomede;

use Closure;
use Exception;

class Lightship
{
    private string $domain;

    /**
     * @var array<Page>
     */
    private array $pages;

    /** 
     * @var array<Report>
     */
    private array $reports;
    private Closure $onReportedPageCallback;

    public function __construct()
    {
        $this->domain = "";
        $this->pages = [];
        $this->reports = [];
        $this->onReportedPageCallback = function (): void {
        };
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
     * @todo Raise if page already added.
     */
    public function page(string $path, array $queries = []): self
    {
        $page = new Page();

        $page->setDomain($this->domain)
            ->setPath($path)
            ->setQueries($queries);

        $this->pages[] = $page;

        return $this;
    }

    public function analyse(): void
    {
        $analyser = new Analyser();

        foreach ($this->pages as $page) {
            assert($page instanceof Page);

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
        $json = json_encode(array_map(fn (Report $report): array => $report->toArray(), $this->reports));

        if (!is_string($json)) {
            throw new Exception(json_last_error_msg());
        }

        return $json;
    }

    public function toPrettyJson(): string
    {
        $json = json_encode(array_map(fn (Report $report): array => $report->toArray(), $this->reports), JSON_PRETTY_PRINT);

        if (!is_string($json)) {
            throw new Exception(json_last_error_msg());
        }

        return $json;
    }
}
