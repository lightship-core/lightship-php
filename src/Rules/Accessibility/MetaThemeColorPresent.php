<?php

namespace Khalyomede\Rules\Accessibility;

use DOMAttr;
use DOMDocument;
use DOMNode;
use Khalyomede\Rules\BaseRule;
use Khalyomede\RuleType;

class MetaThemeColorPresent extends BaseRule
{
    public function __construct()
    {
        $this->name = "metaThemeColorPresent";
        $this->value = 12;
        $this->type = RuleType::Accessibility;
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

            if (!($name instanceof DOMAttr)) {
                continue;
            }

            if ($name->nodeValue !== "theme-color") {
                continue;
            }

            $content = $element->attributes->getNamedItem("content");

            if (!($content instanceof DOMAttr)) {
                continue;
            }

            if (!empty(trim($content->nodeValue))) {
                libxml_clear_errors();

                return true;
            }
        }

        libxml_clear_errors();

        return false;
    }
}
