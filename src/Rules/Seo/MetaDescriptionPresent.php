<?php

namespace Khalyomede\Rules\Seo;

use DOMAttr;
use DOMDocument;
use DOMNode;
use Khalyomede\Rules\BaseRule;
use Khalyomede\RuleType;

class MetaDescriptionPresent extends BaseRule
{
    public function __construct()
    {
        $this->name = "metaDescriptionPresent";
        $this->value = 25;
        $this->type = RuleType::Seo;
    }

    protected function passes(): bool
    {
        libxml_use_internal_errors(true);

        $dom = new DOMDocument();

        $dom->loadHtml((string) $this->response->getBody());

        $elements = $dom->getElementsByTagName("meta");

        foreach (range(0, $elements->count()) as $index) {
            $element = $elements->item($index);

            if (!($element instanceof DOMNode)) {
                continue;
            }

            $name = $element->attributes->getNamedItem("name");

            if (!($name instanceof DOMAttr) || empty(trim($name->nodeValue))) {
                continue;
            }

            if ($name->nodeValue === "description") {
                $value = $element->attributes->getNamedItem("value");

                if (!($value instanceof DOMAttr) || empty(trim($value->nodeValue))) {
                    libxml_clear_errors();

                    return false;
                }

                libxml_clear_errors();

                return !empty(trim($value->nodeValue));
            }
        }

        libxml_clear_errors();

        return false;
    }
}
