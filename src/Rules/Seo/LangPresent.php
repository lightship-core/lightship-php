<?php

namespace Lightship\Rules\Seo;

use DOMAttr;
use DOMDocument;
use DOMNamedNodeMap;
use DOMNode;
use Lightship\Rules\BaseRule;
use Lightship\RuleType;

class LangPresent extends BaseRule
{
    public function __construct()
    {
        $this->value = 25;
        $this->type = RuleType::Seo;
    }

    public function name(): string
    {
        return "langPresent";
    }

    protected function passes(): bool
    {
        libxml_use_internal_errors(true);

        $dom = new DOMDocument();

        $content = (string) $this->response->getBody();

        if (empty(trim($content))) {
            return false;
        }

        $dom->loadHtml($content);

        $element = $dom->getElementsByTagName("html")->item(0);

        if (!($element instanceof DOMNode)) {
            libxml_clear_errors();

            return false;
        }

        libxml_clear_errors();

        return $element->attributes instanceof DOMNamedNodeMap &&
            $element->attributes->getNamedItem("lang") instanceof DOMAttr &&
            !empty($element->attributes->getNamedItem("lang")->nodeValue);
    }
}
