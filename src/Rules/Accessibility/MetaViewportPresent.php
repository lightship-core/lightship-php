<?php

namespace Lightship\Rules\Accessibility;

use DOMAttr;
use DOMDocument;
use DOMNode;
use Lightship\Rules\BaseRule;
use Lightship\RuleType;

class MetaViewportPresent extends BaseRule
{
    public function __construct()
    {
        $this->name = "metaViewportPresent";
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

            if (!($name instanceof DOMAttr) || empty(trim($name->nodeValue))) {
                continue;
            }

            if ($name->nodeValue === "viewport") {
                $content = $element->attributes->getNamedItem("content");

                if (!($content instanceof DOMAttr) || empty(trim($content->nodeValue))) {
                    libxml_clear_errors();

                    return false;
                }

                libxml_clear_errors();

                return str_starts_with($content->nodeValue, "width") || str_starts_with($content->nodeValue, "initial-scale");
            }
        }

        libxml_clear_errors();

        return false;
    }
}
