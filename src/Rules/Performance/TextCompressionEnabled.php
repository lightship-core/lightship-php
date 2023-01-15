<?php

namespace Lightship\Rules\Performance;

use Lightship\Rules\BaseRule;
use Lightship\RuleType;

class TextCompressionEnabled extends BaseRule
{
    public function __construct()
    {
        $this->value = 25;
        $this->type = RuleType::Performance;
    }

    public function name(): string
    {
        return "textCompressionEnabled";
    }

    protected function passes(): bool
    {
        foreach ($this->response->getHeaders() as $key => $value) {
            if (!in_array(strtolower($key), ["content-encoding", "x-encoded-content-encoding"], true)) {
                continue;
            }
            if (!isset($value[0])) {
                continue;
            }
            if (!in_array(strtolower($value[0]), ["br", "gzip", "deflate"], true)) {
                continue;
            }
            return true;
        }

        return false;
    }
}
