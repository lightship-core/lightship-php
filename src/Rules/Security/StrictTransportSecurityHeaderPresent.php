<?php

namespace Khalyomede\Rules\Security;

use Khalyomede\Rule;
use Khalyomede\RuleReport;
use Khalyomede\Rules\BaseRule;
use Khalyomede\RuleType;

class StrictTransportSecurityHeaderPresent extends BaseRule implements Rule
{
    public function toReport(): RuleReport
    {
        $report = new RuleReport();

        $passes = $this->passes();

        $report->setRuleType(RuleType::Security)
            ->setName("strictTransportSecurityHeaderPresent")
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
            if (strtolower($key) === "strict-transport-security" && isset($value[0]) && preg_match("/^max-age=\d+/", $value[0]) === 1) {
                return true;
            }
        }

        return false;
    }
}
