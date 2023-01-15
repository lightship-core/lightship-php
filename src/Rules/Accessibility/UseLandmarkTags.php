<?php

namespace Lightship\Rules\Accessibility;

use DOMDocument;
use Lightship\Rules\BaseRule;
use Lightship\RuleType;

class UseLandmarkTags extends BaseRule
{
    public function __construct()
    {
        $this->value = 14;
        $this->type = RuleType::Accessibility;
    }

    public function name(): string
    {
        return "useLandmarkTags";
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

        libxml_clear_errors();
        if ($dom->getElementsByTagName("main")->count() > 0) {
            return true;
        }
        if ($dom->getElementsByTagName("header")->count() > 0) {
            return true;
        }
        if ($dom->getElementsByTagName("nav")->count() > 0) {
            return true;
        }
        return $dom->getElementsByTagName("footer")->count() > 0;
    }
}
