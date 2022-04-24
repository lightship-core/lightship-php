<?php

namespace Lightship\Rules\Accessibility;

use DOMAttr;
use DOMDocument;
use DOMNode;
use Lightship\Rules\BaseRule;
use Lightship\RuleType;

class ImagesHaveAltAttributes extends BaseRule
{
    public function __construct()
    {
        $this->name = "imagesHaveAltAttributes";
        $this->value = 12;
        $this->type = RuleType::Accessibility;
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

            $alt = $element->attributes->getNamedItem("alt");

            if (!($alt instanceof DOMAttr) || empty(trim($alt->nodeValue))) {
                libxml_clear_errors();

                return false;
            }
        }

        libxml_clear_errors();

        return true;
    }
}
