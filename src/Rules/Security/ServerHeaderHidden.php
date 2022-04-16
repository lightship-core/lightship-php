<?php

namespace Khalyomede\Rules\Security;

use GuzzleHttp\Psr7\Response;
use Khalyomede\Rule;
use Khalyomede\RuleReport;
use Khalyomede\RuleType;
use Psr\Http\Message\ResponseInterface;

class ServerHeaderHidden implements Rule
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
            ->setName("serverHeaderHidden")
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
            if (strtolower($key) === "server" && (!isset($value[0]) || !empty(trim($value[0])))) {
                return false;
            }
        }

        return true;
    }
}
