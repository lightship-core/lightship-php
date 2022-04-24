<?php

namespace Lightship\Rules\Accessibility;

use DOMAttr;
use DOMDocument;
use DOMNode;
use Lightship\Rules\BaseRule;
use Lightship\RuleType;

class IdsAreUnique extends BaseRule
{
    protected array $ids = [];

    public function __construct()
    {
        $this->name = "idsAreUnique";
        $this->value = 13;
        $this->type = RuleType::Accessibility;
        $this->ids = [];
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

        $elements = $dom->getElementsByTagName("*");

        if ($elements->count() === 0) {
            return true;
        }

        foreach (range(0, $elements->count()) as $index) {
            $element = $elements->item($index);

            if (!($element instanceof DOMNode)) {
                continue;
            }

            $id = $element->attributes->getNamedItem("id");

            if (!($id instanceof DOMAttr) || empty($id->nodeValue)) {
                continue;
            }

            if (!in_array($id->nodeValue, $this->ids)) {
                $this->ids[] = $id->nodeValue;

                continue;
            }

            return false;
        }

        libxml_clear_errors();

        return true;
    }
}
