<?php

namespace Khalyomede;

interface Rule
{
    public function __construct();

    public static function fromResponse(Response $response): self;

    public function toReport(): RuleReport;

    public function type(): RuleType;
}
