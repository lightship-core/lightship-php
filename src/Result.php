<?php

namespace Lightship;

/**
 * Represents a single item within a Report.
 *
 * For example, "titlePresent", or "langPresent".
 */
class Result
{
    public function __construct(
        public readonly string $name,
        public readonly bool $passes,
        public readonly RuleType $ruleType,
    ) {
    }

    /**
     * @return array{name: string, passes: bool}
     */
    public function toArray(): array
    {
        return  [
            "name" => $this->name,
            "passes" => $this->passes,
        ];
    }
}
