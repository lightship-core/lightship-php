<?php

namespace Khalyomede\Rules\Performance;

use Khalyomede\Rule;
use Khalyomede\RuleReport;
use Khalyomede\Rules\BaseRule;
use Khalyomede\RuleType;

class UsesHttp2 extends BaseRule implements Rule
{
    public function ruleType(): RuleType
    {
        return RuleType::Performance;
    }

    public function toReport(): RuleReport
    {
        $report = new RuleReport();

        $passes = $this->passes();

        $report->setRuleType($this->ruleType())
            ->setName("usesHttp2")
            ->setPasses($passes)
            ->setScore($passes ? 25 : 0);

        return $report;
    }

    public function passes(): bool
    {
        return ($this->response->getProtocolVersion()[0] ?? 1) > 1;
    }
}