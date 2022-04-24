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

        $content = (string) $this->response->getBody();

        if (empty(trim($content))) {
            return false;
        }

        $dom->loadHtml($content);

        $elements = $dom->getElementsByTagName("meta");

        if ($elements->count() === 0) {
            return true;
        }

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
                $content = $element->attributes->getNamedItem("content");

                if (!($content instanceof DOMAttr) || empty(trim($content->nodeValue))) {
                    libxml_clear_errors();

                    return false;
                }

                libxml_clear_errors();

                return !empty(trim($content->nodeValue));
            }
        }

        libxml_clear_errors();

        return false;
    }
}
