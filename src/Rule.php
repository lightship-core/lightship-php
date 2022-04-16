<?php

namespace Khalyomede;

use Psr\Http\Message\ResponseInterface;

interface Rule
{
    public static function fromResponse(ResponseInterface $response): self;

    public function toReport(): RuleReport;

    public function ruleType(): RuleType;
}
