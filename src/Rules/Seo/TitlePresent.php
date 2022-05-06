<?php

namespace Lightship\Rules\Seo;

use DOMDocument;
use DOMNode;
use Lightship\Rules\BaseRule;
use Lightship\RuleType;

class TitlePresent extends BaseRule
{
    public function __construct()
    {
        $this->value = 25;
        $this->type = RuleType::Seo;
    }

    public function name(): string
    {
        return "titlePresent";
    }

    protected function passes(): bool
    {
        libxml_use_internal_errors(true);

        $dom = new DOMDocument();

        $content = (string) $this->response->getBody();

        if (empty(trim($content))) {
            return false;
        }

        $dom->loadHtml($content);

        $element = $dom->getElementsByTagName("title")->item(0);

        if (!($element instanceof DOMNode)) {
            libxml_clear_errors();

            return false;
        }

        libxml_clear_errors();

        return !empty($element->textContent);
    }
}
