<?php

namespace Khalyomede\Rules;

use GuzzleHttp\Psr7\Response;
use Khalyomede\Rule;
use Khalyomede\RuleReport;
use Khalyomede\RuleType;
use Psr\Http\Message\ResponseInterface;

class BaseRule implements Rule
{
    protected ResponseInterface $response;

    public function __construct()
    {
        $this->response = new Response();
    }

    public static function fromResponse(ResponseInterface $response): self
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
