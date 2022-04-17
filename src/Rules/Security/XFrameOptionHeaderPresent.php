<?php

namespace Khalyomede\Rules\Security;

use Khalyomede\Rule;
use Khalyomede\RuleReport;
use Khalyomede\Rules\BaseRule;
use Khalyomede\RuleType;

class XFrameOptionHeaderPresent extends BaseRule implements Rule
{
    public function toReport(): RuleReport
    {
        $report = new RuleReport();

        $passes = $this->passes();

        $report->setRuleType(RuleType::Security)
            ->setName("xFrameOptionsPresent")
            ->setPasses($passes)
            ->setScore($passes ? 25 : 0);

        return $report;
    }

    public function ruleType(): RuleType
    {
        return RuleType::Security;
    }

    private function passes(): bool
    {
        foreach ($this->response->getHeaders() as $key => $value) {
            if (strtolower($key) === "x-frame-options" && isset($value[0]) && in_array(strtolower($value[0]), ["deny", "sameorigin"], true)) {
                return true;
            }
        }

        return false;
    }
}
