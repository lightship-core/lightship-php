<?php

namespace Khalyomede\Rules\Seo;

use DOMAttr;
use DOMDocument;
use DOMNode;
use Khalyomede\Rules\BaseRule;
use Khalyomede\RuleType;

class LinksDefineHref extends BaseRule
{
    public function __construct()
    {
        $this->name = "linksDefineHref";
        $this->value = 25;
        $this->type = RuleType::Seo;
    }

    protected function passes(): bool
    {
        libxml_use_internal_errors(true);

        $dom = new DOMDocument();
        $allTagsDefineHref = true;

        $dom->loadHtml((string) $this->response->getBody());

        $elements = $dom->getElementsByTagName("a");

        foreach (range(0, $elements->count()) as $index) {
            $element = $elements->item($index);

            if (!($element instanceof DOMNode)) {
                continue;
            }

            $attribute = $element->attributes->getNamedItem("href");

            if (!($attribute instanceof DOMAttr) || empty(trim($attribute->nodeValue))) {
                $allTagsDefineHref = false;
            }
        }

        libxml_clear_errors();

        return $allTagsDefineHref;
    }
}
