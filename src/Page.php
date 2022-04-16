<?php

namespace Khalyomede;

class Page
{
    private string $domain;
    private string $path;

    /**
     * @var array<string, string>
     */
    private array $queries;

    public function __construct()
    {
        $this->domain = "";
        $this->path = "";
        $this->queries = [];
    }

    public function setDomain(string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @param array<string, string> $queries
     */
    public function setQueries(array $queries): self
    {
        $this->queries = $queries;

        return $this;
    }

    public function url(): string
    {
        return $this->domain . (empty($this->domain) || str_ends_with($this->domain, "/") ? "" : "/") . ltrim($this->path, "/") . (empty($this->queries) ? "" : "?") . http_build_query($this->queries);
    }

    /**
     * @return array<string, string>
     */
    public function queries(): array
    {
        return $this->queries;
    }
}
