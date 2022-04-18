<?php

namespace Khalyomede\Rules;

use Khalyomede\Response;
use Khalyomede\Rule;
use Khalyomede\RuleReport;
use Khalyomede\RuleType;

class BaseRule implements Rule
{
    protected Response $response;

    public function __construct()
    {
        $this->response = new Response();
    }

    public static function fromResponse(Response $response): self
    {
        $instance = new static();

        $instance->response = $response;

        return $instance;
    }

    public function toReport(): RuleReport
    {
        return new RuleReport();
    }

    public function ruleType(): RuleType
    {
        return RuleType::Unknown;
    }
}
