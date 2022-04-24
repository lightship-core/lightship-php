<?php

namespace Khalyomede\Rules\Accessibility;

use DOMAttr;
use DOMDocument;
use DOMNode;
use Khalyomede\Rules\BaseRule;
use Khalyomede\RuleType;

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

            $ariaLabel = $element->attributes->getNamedItem("aria-label");

            if (!($ariaLabel instanceof DOMAttr)) {
                libxml_clear_errors();

                return false;
            }

            if (empty($ariaLabel)) {
                libxml_clear_errors();

                return false;
            }
        }

        libxml_clear_errors();

        return true;
    }
}
