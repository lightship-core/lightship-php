<?php

namespace Lightship\Rules\Performance;

use Lightship\Rules\BaseRule;
use Lightship\RuleType;

class NoRedirects extends BaseRule
{
    public function __construct()
    {
        $this->value = 25;
        $this->type = RuleType::Performance;
    }

    public function name(): string
    {
        return "noRedirects";
    }

    protected function passes(): bool
    {
        return empty($this->response->getHeaderLine("X-Guzzle-Redirect-History"));
    }
}
