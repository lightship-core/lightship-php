<?php

namespace Lightship\Rules\Accessibility;

use Lightship\Rules\BaseRule;
use Lightship\RuleType;

class DoctypeHtmlPresent extends BaseRule
{
    public function __construct()
    {
        $this->name = "doctypeHtmlPresent";
        $this->value = 13;
        $this->type = RuleType::Accessibility;
    }

    protected function passes(): bool
    {
        return str_starts_with($this->response->getBody(), "<!DOCTYPE html");
    }
}
