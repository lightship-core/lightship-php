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

        $dom->loadHtml((string) $this->response->getBody());

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
                return false;
            }

            if (!empty($element->nodeValue)) {
                continue;
            }

            $ariaLabel = $element->attributes->getNamedItem("aria-label");

            if (!($name instanceof DOMAttr) || empty(trim($name->nodeValue))) {
                return false;
            }

            if (empty($ariaLabel)) {
                return false;
            }
        }

        libxml_clear_errors();



        return true;
    }
}
