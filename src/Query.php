<?php

namespace Khalyomede;

use Webmozart\Assert\Assert;

class Query
{
    private string $key;
    private string $value;

    public function __construct()
    {
    }

    public function setKey(string $key): self
    {
        Assert::notEmpty($key);

        $this->key = filter_var($key, FILTER_SANITIZE_ENCODED);

        return $this;
    }

    public function setValue(string $value): self
    {
        Assert::notEmpty($value);

        $this->value = filter_var($value, FILTER_SANITIZE_ENCODED);

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
