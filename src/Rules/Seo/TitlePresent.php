<?php

namespace Khalyomede\Rules\Seo;

use DOMDocument;
use DOMNode;
use Khalyomede\Rules\BaseRule;
use Khalyomede\RuleType;

class TitlePresent extends BaseRule
{
    public function __construct()
    {
        $this->name = "titlePresent";
        $this->value = 34;
        $this->type = RuleType::Seo;
    }

    protected function passes(): bool
    {
        libxml_use_internal_errors(true);

        $dom = new DOMDocument();

        $dom->loadHtml((string) $this->response->getBody());

        $element = $dom->getElementsByTagName("title")->item(0);

        if (!($element instanceof DOMNode)) {
            libxml_clear_errors();

            return false;
        }

        libxml_clear_errors();

        return !empty($element->textContent);
    }
}
