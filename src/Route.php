<?php

namespace Khalyomede;

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
        $this->path = $path;

        return $this;
    }

    /**
     * @param array<int, array<string, string>> $queries
     */
    public function setQueries(array $queries): self
    {
        $this->queries = array_map(fn (array $query): Query => (new Query())->setKey($query["key"])->setValue($query["value"]), $queries);

        return $this;
    }

    public function path(): string
    {
        return $this->path;
    }

    /**
     * @return array<Query>
     */
    public function queries(): array
    {
        return $this->queries;
    }
}
