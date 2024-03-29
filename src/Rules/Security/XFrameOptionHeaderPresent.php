<?php

namespace Lightship\Rules\Security;

use Lightship\Rules\BaseRule;
use Lightship\RuleType;

class XFrameOptionHeaderPresent extends BaseRule
{
    public function __construct()
    {
        $this->value = 25;
        $this->type = RuleType::Security;
    }

    public function name(): string
    {
        return "xFrameOptionsPresent";
    }

    protected function passes(): bool
    {
        foreach ($this->response->getHeaders() as $key => $value) {
            if (strtolower($key) !== "x-frame-options") {
                continue;
            }
            if (!isset($value[0])) {
                continue;
            }
            if (!in_array(strtolower($value[0]), ["deny", "sameorigin"], true)) {
                continue;
            }
            return true;
        }

        return false;
    }
}
