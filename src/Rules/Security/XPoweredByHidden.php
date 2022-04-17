<?php

namespace Khalyomede\Rules\Security;

use Khalyomede\Rules\BaseRule;
use Khalyomede\Rule;
use Khalyomede\RuleReport;
use Khalyomede\RuleType;

class XPoweredByHidden extends BaseRule implements Rule
{
    public function toReport(): RuleReport
    {
        $report = new RuleReport();

        $passes = $this->passes();

        $report->setRuleType(RuleType::Security)
            ->setName("xPoweredByHidden")
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
        $headers = $this->response->getHeaders();

        foreach ($headers as $key => $value) {
            if (strtolower($key) === "x-powered-by" && (!isset($value[0]) || !empty(trim($value[0])))) {
                return false;
            }
        }

        return true;
    }
}
