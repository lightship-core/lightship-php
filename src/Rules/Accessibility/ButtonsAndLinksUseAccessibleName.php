<?php

namespace Lightship\Rules\Accessibility;

use DOMAttr;
use DOMDocument;
use DOMNamedNodeMap;
use DOMNode;
use Lightship\Rules\BaseRule;
use Lightship\RuleType;

class ButtonsAndLinksUseAccessibleName extends BaseRule
{
    public function __construct()
    {
        $this->name = "buttonsAndLinksUseAccessibleName";
        $this->value = 13;
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

        return $this->elementsHaveAccessibleName($dom, "button")
            && $this->elementsHaveAccessibleName($dom, "a");
    }

    protected function elementsHaveAccessibleName(DOMDocument $dom, string $name): bool
    {
        $elements = $dom->getElementsByTagName($name);

        if ($elements->count() === 0) {
            return true;
        }

        foreach (range(0, $elements->count()) as $index) {
            $element = $elements->item($index);

            if (!($element instanceof DOMNode)) {
                continue;
            }

            if (!empty($element->nodeValue)) {
                continue;
            }

            $attributes = $element->attributes;

            if (!($attributes instanceof DOMNamedNodeMap)) {
                continue;
            }

            $ariaLabel = $attributes->getNamedItem("aria-label");

            if (!($ariaLabel instanceof DOMAttr)) {
                libxml_clear_errors();

                return false;
            }

            if (empty($ariaLabel->nodeValue ?? "")) {
                libxml_clear_errors();

                return false;
            }
        }

        libxml_clear_errors();

        return true;
    }
}
