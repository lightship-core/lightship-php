<?php

namespace Lightship\Rules\Accessibility;

use Lightship\Rules\BaseRule;
use Lightship\RuleType;

class DoctypeHtmlPresent extends BaseRule
{
    public function __construct()
    {
        $this->value = 14;
        $this->type = RuleType::Accessibility;
    }

    public function name(): string
    {
        return "doctypeHtmlPresent";
    }

    protected function passes(): bool
    {
        return str_starts_with($this->response->getBody(), "<!DOCTYPE html");
    }
}
