<?php

namespace Lightship\Rules\Performance;

use Lightship\Rules\BaseRule;
use Lightship\RuleType;

class FastResponseTime extends BaseRule
{
    public function __construct()
    {
        $this->name = "fastResponseTime";
        $this->value = 25;
        $this->type = RuleType::Performance;
    }

    protected function passes(): bool
    {
        return $this->response->getResponseTimeInSeconds() <= 1.0;
    }
}
