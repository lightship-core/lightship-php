<?php

namespace Lightship\Rules\Performance;

use Lightship\Rules\BaseRule;
use Lightship\RuleType;

class FastResponseTime extends BaseRule
{
    public function __construct()
    {
        $this->value = 25;
        $this->type = RuleType::Performance;
    }

    public function name(): string
    {
        return "fastResponseTime";
    }

    protected function passes(): bool
    {
        return $this->response->getResponseTimeInSeconds() <= 1.0;
    }
}
