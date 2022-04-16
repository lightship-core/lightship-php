<?php

namespace Khalyomede;

class Route
{
    private string $path;
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

    public function setQueries(array $queries): self
    {
        $this->queries = array_map(fn (array $query): Query => (new Query())->setKey($query["key"])->setValue($query["value"]), $queries);

        return $this;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function queries(): array
    {
        return $this->queries;
    }
}
