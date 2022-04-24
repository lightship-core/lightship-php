<?php

namespace Khalyomede;

class Report
{
    private string $url;
    private float $durationInSeconds;

    /**
     * @var array<RuleReport>
     */
    private array $ruleReports;

    public function __construct()
    {
        $this->url = "";
        $this->durationInSeconds = 0.0;
        $this->ruleReports = [];
    }

    public function url(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function durationInSeconds(float $duration): self
    {
        $this->durationInSeconds = $duration;

        return $this;
    }

    /**
     * @param array<RuleReport> $ruleReports
     */
    public function setRuleReports(array $ruleReports): self
    {
        $this->ruleReports = $ruleReports;

        return $this;
    }

    /**
     * @return array<mixed>
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
            "seo" => $this->results(RuleType::Seo),
            "security" => $this->results(RuleType::Security),
            "performance" => $this->results(RuleType::Performance),
            "accessibility" => $this->results(RuleType::Accessibility),
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
     * @return array<int, array<string, bool|string>>
     */
    public function results(RuleType $ruleType): array
    {
        return array_map(
            fn (RuleReport $report): array => [
                "name" => $report->name(),
                "passes" => $report->passes(),
            ],
            array_values(
                array_filter(
                    $this->ruleReports,
                    fn (RuleReport $report): bool => $report->ruleType() === $ruleType
                )
            )
        );
    }
}
