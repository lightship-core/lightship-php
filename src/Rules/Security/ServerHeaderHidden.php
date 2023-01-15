<?php

namespace Lightship\Rules\Security;

use Lightship\Rules\BaseRule;
use Lightship\RuleType;

class ServerHeaderHidden extends BaseRule
{
    public function __construct()
    {
        $this->value = 25;
        $this->type = RuleType::Security;
    }

    public function name(): string
    {
        return "serverHeaderHidden";
    }

    protected function passes(): bool
    {
        $headers = $this->response->getHeaders();

        foreach ($headers as $key => $value) {
            if (strtolower($key) !== "server") {
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
