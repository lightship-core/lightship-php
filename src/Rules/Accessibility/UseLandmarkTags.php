<?php

namespace Khalyomede\Rules\Accessibility;

use DOMDocument;
use Khalyomede\Rules\BaseRule;
use Khalyomede\RuleType;

class UseLandmarkTags extends BaseRule
{
    public function __construct()
    {
        $this->name = "useLandmarkTags";
        $this->value = 12;
        $this->type = RuleType::Accessibility;
    }

    protected function passes(): bool
    {
        libxml_use_internal_errors(true);

        $dom = new DOMDocument();

        $dom->loadHtml((string) $this->response->getBody());

        libxml_clear_errors();

        return $dom->getElementsByTagName("main")->count() > 0
            || $dom->getElementsByTagName("header")->count() > 0
            || $dom->getElementsByTagName("nav")->count() > 0
            || $dom->getElementsByTagName("footer")->count() > 0;
    }
}
