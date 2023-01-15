<?php

namespace Lightship\Rules\Seo;

use DOMAttr;
use DOMDocument;
use DOMNamedNodeMap;
use DOMNode;
use Lightship\Rules\BaseRule;
use Lightship\RuleType;

class MetaDescriptionPresent extends BaseRule
{
    public function __construct()
    {
        $this->value = 25;
        $this->type = RuleType::Seo;
    }

    public function name(): string
    {
        return "metaDescriptionPresent";
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
            if (!($name instanceof DOMAttr)) {
                continue;
            }
            if (empty(trim($name->nodeValue ?? ""))) {
                continue;
            }

            if ($name->nodeValue === "description") {
                $content = $attributes->getNamedItem("content");

                if (!($content instanceof DOMAttr)) {
                    libxml_clear_errors();

                    return false;
                }

                libxml_clear_errors();

                return mb_strlen(trim($content->nodeValue ?? "")) > 0;
            }
        }

        libxml_clear_errors();

        return false;
    }
}
