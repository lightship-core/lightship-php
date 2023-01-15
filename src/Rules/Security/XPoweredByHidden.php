<?php

namespace Lightship\Rules\Security;

use Lightship\Rules\BaseRule;
use Lightship\RuleType;

class XPoweredByHidden extends BaseRule
{
    public function __construct()
    {
        $this->value = 25;
        $this->type = RuleType::Security;
    }

    public function name(): string
    {
        return "xPoweredByHidden";
    }

    protected function passes(): bool
    {
        $headers = $this->response->getHeaders();

        foreach ($headers as $key => $value) {
            if (strtolower($key) !== "x-powered-by") {
                continue;
            }
            if (!(!isset($value[0]) || !empty(trim($value[0])))) {
                continue;
            }
            return false;
        }

        return true;
    }
}
