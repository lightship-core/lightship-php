<?php

namespace Khalyomede\Rules\Performance;

use Khalyomede\Rule;
use Khalyomede\RuleReport;
use Khalyomede\Rules\BaseRule;
use Khalyomede\RuleType;

class TextCompressionEnabled extends BaseRule implements Rule
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
            ->setName("textCompressionEnabled")
            ->setPasses($passes)
            ->setScore($passes ? 25 : 0);

        return $report;
    }

    public function passes(): bool
    {
        foreach ($this->response->getHeaders() as $key => $value) {
            if (in_array(strtolower($key), ["content-encoding", "x-encoded-content-encoding"], true) && isset($value[0]) && in_array(strtolower($value[0]), ["br", "gzip", "deflate"], true)) {
                return true;
            }
        }

        return false;
    }
}
