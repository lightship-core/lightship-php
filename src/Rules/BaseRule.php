<?php

namespace Lightship\Rules;

use Lightship\Response;
use Lightship\Rule;
use Lightship\RuleReport;
use Lightship\RuleType;

class BaseRule implements Rule
{
    protected Response $response;

    protected string $name;
    protected int $value;
    protected RuleType $type;

    public function __construct()
    {
        $this->response = new Response();
        $this->name = "";
        $this->value = 0;
        $this->type = RuleType::Unknown;
    }

    public static function fromResponse(Response $response): self
    {
        $instance = new static();

        $instance->response = $response;

        return $instance;
    }

    public function toReport(): RuleReport
    {
        $report = new RuleReport();

        $passes = $this->passes();

        $report->setRuleType($this->type)
            ->setName($this->name)
            ->setPasses($passes)
            ->setScore($passes ? $this->value : 0);

        return $report;
    }

    public function type(): RuleType
    {
        return $this->type;
    }

    public function value(): int
    {
        return $this->value;
    }

    protected function passes(): bool
    {
        return false;
    }
}
