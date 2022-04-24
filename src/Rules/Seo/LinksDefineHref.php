<?php

namespace Lightship\Rules\Seo;

use DOMAttr;
use DOMDocument;
use DOMNode;
use Lightship\Rules\BaseRule;
use Lightship\RuleType;

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

        $content = (string) $this->response->getBody();

        if (empty(trim($content))) {
            return false;
        }

        $dom->loadHtml($content);

        $elements = $dom->getElementsByTagName("a");

        if ($elements->count() === 0) {
            return true;
        }

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
