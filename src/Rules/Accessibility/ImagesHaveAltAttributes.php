<?php

namespace Lightship\Rules\Accessibility;

use DOMAttr;
use DOMDocument;
use DOMNamedNodeMap;
use DOMNode;
use Lightship\Rules\BaseRule;
use Lightship\RuleType;

class ImagesHaveAltAttributes extends BaseRule
{
    public function __construct()
    {
        $this->value = 14;
        $this->type = RuleType::Accessibility;
    }

    public function name(): string
    {
        return "imagesHaveAltAttributes";
    }

    protected function passes(): bool
    {
        libxml_use_internal_errors(true);

        $dom = new DOMDocument();

        $content = (string) $this->response->getBody();

        if (empty(trim($content))) {
            return true;
        }

        $dom->loadHtml($content);

        $elements = $dom->getElementsByTagName("img");

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

            $alt = $attributes->getNamedItem("alt");

            if (!($alt instanceof DOMAttr) || empty(trim($alt->nodeValue ?? ""))) {
                libxml_clear_errors();

                return false;
            }
        }

        libxml_clear_errors();

        return true;
    }
}
