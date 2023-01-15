<?php

namespace Lightship\Rules\Accessibility;

use DOMAttr;
use DOMDocument;
use DOMNamedNodeMap;
use DOMNode;
use Lightship\Rules\BaseRule;
use Lightship\RuleType;

class IdsAreUnique extends BaseRule
{
    /**
     * @var array<string>
     */
    protected array $ids = [];

    public function __construct()
    {
        $this->value = 15;
        $this->type = RuleType::Accessibility;
        $this->ids = [];
    }

    public function name(): string
    {
        return "idsAreUnique";
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

            $attributes = $element->attributes;

            if (!($attributes instanceof DOMNamedNodeMap)) {
                continue;
            }

            $id = $attributes->getNamedItem("id");
            if (!($id instanceof DOMAttr)) {
                continue;
            }
            if (empty($id->nodeValue)) {
                continue;
            }

            if (!in_array($id->nodeValue, $this->ids, true)) {
                $this->ids[] = $id->nodeValue;

                continue;
            }

            return false;
        }

        libxml_clear_errors();

        return true;
    }
}
