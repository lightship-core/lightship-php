<?php

namespace Lightship\Rules\Accessibility;

use DOMAttr;
use DOMDocument;
use DOMNamedNodeMap;
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

            $attributes = $element->attributes;

            if (!($attributes instanceof DOMNamedNodeMap)) {
                continue;
            }

            $name = $attributes->getNamedItem("name");

            if (!($name instanceof DOMAttr) || empty(trim($name->nodeValue ?? ""))) {
                continue;
            }

            if ($name->nodeValue === "viewport") {
                $content = $attributes->getNamedItem("content");

                if (!($content instanceof DOMAttr) || empty(trim($content->nodeValue ?? ""))) {
                    libxml_clear_errors();

                    return false;
                }

                libxml_clear_errors();

                $value = $content->nodeValue ?? "";

                return str_starts_with($value, "width") || str_starts_with($value, "initial-scale");
            }
        }

        libxml_clear_errors();

        return false;
    }
}
