<?php

namespace Lightship;

use RuntimeException;
use Webmozart\Assert\Assert;

class Query
{
    private string $key;
    private string $value;

    public function __construct()
    {
        $this->key = "";
        $this->value = "";
    }

    public function setKey(string $key): self
    {
        Assert::notEmpty($key);

        $filteredKey = filter_var($key, FILTER_SANITIZE_ENCODED);

        if (!is_string($filteredKey)) {
            throw new RuntimeException("Cannot filter query key");
        }

        $this->key = $filteredKey;

        return $this;
    }

    public function setValue(string $value): self
    {
        Assert::notEmpty($value);

        $filteredValue = filter_var($value, FILTER_SANITIZE_ENCODED);

        if (!is_string($filteredValue)) {
            throw new RuntimeException("Cannot filter query value");
        }

        $this->value = $filteredValue;

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
