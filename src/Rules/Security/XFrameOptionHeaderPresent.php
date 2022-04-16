<?php

namespace Khalyomede\Rules\Security;

use GuzzleHttp\Psr7\Response;
use Khalyomede\Rule;
use Khalyomede\RuleReport;
use Khalyomede\RuleType;
use Psr\Http\Message\ResponseInterface;

class XFrameOptionHeaderPresent implements Rule
{
    private ResponseInterface $response;

    public function __construct()
    {
        $this->response = new Response();
    }

    public static function fromResponse(ResponseInterface $response): self
    {
        $instance = new self();

        $instance->response = $response;

        return $instance;
    }

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
            if (strtolower($key) === "x-frame-options") {
                return true;
            }
        }

        return false;
    }
}
