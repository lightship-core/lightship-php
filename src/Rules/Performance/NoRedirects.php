<?php

namespace Lightship\Rules\Performance;

use Lightship\Rules\BaseRule;
use Lightship\RuleType;

class NoRedirects extends BaseRule
{
    public function __construct()
    {
        $this->name = "noRedirects";
        $this->value = 25;
        $this->type = RuleType::Performance;
    }

    protected function passes(): bool
    {
        return empty($this->response->getHeaderLine("X-Guzzle-Redirect-History"));
    }
}
