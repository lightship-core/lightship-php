<?php

namespace Khalyomede;

class Query
{
    private string $key;
    private string $value;

    public function __construct()
    {
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function value(): string
    {
        return $this->value;
    }
}
