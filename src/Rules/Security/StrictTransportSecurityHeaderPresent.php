<?php

namespace Khalyomede\Rules\Security;

use Khalyomede\Rules\BaseRule;
use Khalyomede\RuleType;

class StrictTransportSecurityHeaderPresent extends BaseRule
{
    public function __construct()
    {
        $this->name = "strictTransportSecurityHeaderPresent";
        $this->value = 25;
        $this->type = RuleType::Security;
    }

    protected function passes(): bool
    {
        $headers = $this->response->getHeaders();

        foreach ($headers as $key => $value) {
            if (strtolower($key) === "strict-transport-security" && isset($value[0]) && preg_match("/^max-age=\d+/", $value[0]) === 1) {
                return true;
            }
        }

        return false;
    }
}
