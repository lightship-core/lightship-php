<?php

namespace Lightship;

use Webmozart\Assert\Assert;

class Route
{
    private string $path;

    /**
     * @var array<Query>
     */
    private array $queries;

    public function __construct()
    {
        $this->path = "";
        $this->queries = [];
    }

    public function setPath(string $path): self
    {
        Assert::notEmpty($path);

        $this->path = $path;

        return $this;
    }

    /**
     * @param array<Query> $queries
     */
    public function setQueries(array $queries): self
    {
        $this->queries = $queries;

        return $this;
    }

    public function path(): string
    {
        return $this->path . ($this->hasQueries() ? "?" . http_build_query($this->queriesList()) : "");
    }

    /**
     * @return array<Query>
     */
    public function queries(): array
    {
        return $this->queries;
    }

    /**
     * @return array<string, string>
     */
    public function queriesList(): array
    {
        $list = [];

        foreach ($this->queries as $query) {
            $list[$query->key()] = $query->value();
        }

        return $list;
    }

    protected function hasQueries(): bool
    {
        return count($this->queries) > 0;
    }
}
