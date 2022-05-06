<?php

namespace Lightship\Rules\Performance;

use Lightship\Rules\BaseRule;
use Lightship\RuleType;

class UsesHttp2 extends BaseRule
{
    public function __construct()
    {
        $this->value = 25;
        $this->type = RuleType::Performance;
    }

    public function name(): string
    {
        return "usesHttp2";
    }

    public function passes(): bool
    {
        return ($this->response->getProtocolVersion()[0] ?? 1) > 1;
    }
}
