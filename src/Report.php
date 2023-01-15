<?php

namespace Lightship;

class Report
{
    public function __construct(
        public readonly string $url,
        public readonly float $durationInSeconds,
        /**
         * @var array<RuleReport>
         */
        private readonly array $ruleReports
    ) {
    }

    /**
     * @return array{url: string, durationInSeconds: float, scores: array{seo: int, security: int, performance: int, accessibility: int}, seo: array<array{name: string, passes: bool}>, security: array<array{name: string, passes: bool}>, performance: array<array{name: string, passes: bool}>, accessibility: array<array{name: string, passes: bool}>}
     */
    public function toArray(): array
    {
        return [
            "url" => $this->url,
            "durationInSeconds" => $this->durationInSeconds,
            "scores" => [
                "seo" => $this->score(RuleType::Seo),
                "security" => $this->score(RuleType::Security),
                "performance" => $this->score(RuleType::Performance),
                "accessibility" => $this->score(RuleType::Accessibility),
            ],
            "seo" => $this->resultsList(RuleType::Seo),
            "security" => $this->resultsList(RuleType::Security),
            "performance" => $this->resultsList(RuleType::Performance),
            "accessibility" => $this->resultsList(RuleType::Accessibility),
        ];
    }

    public function score(RuleType $ruleType): int
    {
        return array_sum(
            array_map(
                fn (RuleReport $report): int => $report->score(),
                array_filter(
                    $this->ruleReports,
                    fn (RuleReport $report): bool => $report->ruleType() === $ruleType
                )
            )
        );
    }

    /**
     * @return array<Result>
     */
    public function results(RuleType $ruleType): array
    {
        return array_map(
            fn (RuleReport $report): Result => new Result($report->name(), $report->passes(), $ruleType),
            array_values(
                array_filter(
                    $this->ruleReports,
                    fn (RuleReport $report): bool => $report->ruleType() === $ruleType
                )
            )
        );
    }

    public function ruleTypePassed(RuleType $ruleType): bool
    {
        return count(array_filter($this->results($ruleType), fn (Result $result): bool => !$result->passes)) === 0;
    }

    public function allRulesPassed(): bool
    {
        if ($this->ruleTypePassed(RuleType::Accessibility)) {
            return true;
        }
        if ($this->ruleTypePassed(RuleType::Performance)) {
            return true;
        }
        if ($this->ruleTypePassed(RuleType::Security)) {
            return true;
        }
        return $this->ruleTypePassed(RuleType::Seo);
    }

    public function rulePassed(Rule $rule): bool
    {
        /**
         * @var array<Result>
         */
        $results = array_values(
            array_filter(
                $this->results($rule->type()),
                fn (Result $result): bool => $result->name === $rule->name()
            )
        );

        if (count($results) === 0) {
            return false;
        }

        $result = $results[0];

        return $result->passes;
    }

    /**
     * @return array<array{name: string, passes: bool}>
     */
    protected function resultsList(RuleType $ruleType): array
    {
        return array_map(fn (Result $result): array => $result->toArray(), $this->results($ruleType));
    }
}
