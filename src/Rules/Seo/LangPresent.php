<?php

namespace Khalyomede\Rules\Seo;

use DOMAttr;
use DOMDocument;
use DOMNamedNodeMap;
use DOMNode;
use Khalyomede\Rules\BaseRule;
use Khalyomede\RuleType;

class LangPresent extends BaseRule
{
    public function __construct()
    {
        $this->name = "langPresent";
        $this->value = 50;
        $this->type = RuleType::Seo;
    }

    protected function passes(): bool
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
            $element->attributes->getNamedItem("lang") instanceof DOMAttr &&
            !empty($element->attributes->getNamedItem("lang")->nodeValue);
    }
}
