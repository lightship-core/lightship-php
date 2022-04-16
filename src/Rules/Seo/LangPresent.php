<?php

namespace Khalyomede\Rules\Seo;

use DOMAttr;
use DOMDocument;
use DOMNamedNodeMap;
use DOMNode;
use GuzzleHttp\Psr7\Response;
use Khalyomede\Rule;
use Khalyomede\RuleReport;
use Khalyomede\RuleType;
use Psr\Http\Message\ResponseInterface;

class LangPresent implements Rule
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

        $report->setRuleType(RuleType::Seo)
            ->setName("langPresent")
            ->setPasses($passes)
            ->setScore($passes ? 50 : 0);

        return $report;
    }

    public function ruleType(): RuleType
    {
        return RuleType::Security;
    }

    private function passes(): bool
    {
        libxml_use_internal_errors(true);

        $dom = new DOMDocument();

        $dom->loadHtml((string) $this->response->getBody());

        $element = $dom->getElementsByTagName("html")->item(0);

        if (!($element instanceof DOMNode)) {
            libxml_clear_errors();

            return false;
        }

        libxml_clear_errors();

        return $element->attributes instanceof DOMNamedNodeMap &&
            $element->attributes->getNamedItem("lang") instanceof DOMAttr;
    }
}
