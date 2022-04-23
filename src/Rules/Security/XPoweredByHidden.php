<?php

namespace Khalyomede\Rules\Security;

use Khalyomede\Rules\BaseRule;
use Khalyomede\RuleType;

class XPoweredByHidden extends BaseRule
{
    public function __construct()
    {
        $this->name = "xPoweredByHidden";
        $this->value = 25;
        $this->type = RuleType::Security;
    }

    protected function passes(): bool
    {
        $headers = $this->response->getHeaders();

        foreach ($headers as $key => $value) {
            if (strtolower($key) === "x-powered-by" && (!isset($value[0]) || !empty(trim($value[0])))) {
                return false;
            }
        }

        return true;
    }
}
