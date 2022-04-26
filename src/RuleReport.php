<?php

namespace Lightship;

class RuleReport
{
    protected string $name;
    protected bool $passes;
    protected int $score;
    protected RuleType $ruleType;

    public function __construct()
    {
        $this->name = "";
        $this->passes = false;
        $this->score = 0;
        $this->ruleType = RuleType::Unknown;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function passes(): bool
    {
        return $this->passes;
    }

    public function score(): int
    {
        return $this->score;
    }

    public function ruleType(): RuleType
    {
        return $this->ruleType;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setPasses(bool $passes): self
    {
        $this->passes = $passes;

        return $this;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function setRuleType(RuleType $ruleType): self
    {
        $this->ruleType = $ruleType;

        return $this;
    }
}
