<?php

namespace Lightship\Rules\Security;

use Lightship\Rules\BaseRule;
use Lightship\RuleType;

class StrictTransportSecurityHeaderPresent extends BaseRule
{
    public function __construct()
    {
        $this->value = 25;
        $this->type = RuleType::Security;
    }

    public function name(): string
    {
        return "strictTransportSecurityHeaderPresent";
    }

    protected function passes(): bool
    {
        $headers = $this->response->getHeaders();

        foreach ($headers as $key => $value) {
            if (strtolower($key) !== "strict-transport-security") {
                continue;
            }
            if (!isset($value[0])) {
                continue;
            }
            if (preg_match("/^max-age=\d+/", $value[0]) !== 1) {
                continue;
            }
            return true;
        }

        return false;
    }
}
