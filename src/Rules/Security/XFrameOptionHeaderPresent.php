<?php

namespace Lightship\Rules\Security;

use Lightship\Rules\BaseRule;
use Lightship\RuleType;

class XFrameOptionHeaderPresent extends BaseRule
{
    public function __construct()
    {
        $this->name = "xFrameOptionsPresent";
        $this->value = 25;
        $this->type = RuleType::Security;
    }

    protected function passes(): bool
    {
        foreach ($this->response->getHeaders() as $key => $value) {
            if (strtolower($key) === "x-frame-options" && isset($value[0]) && in_array(strtolower($value[0]), ["deny", "sameorigin"], true)) {
                return true;
            }
        }

        return false;
    }
}
