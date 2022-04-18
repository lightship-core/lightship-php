<?php

namespace Khalyomede;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\TransferStats;
use Khalyomede\Rules\Performance\FastResponseTime;
use Khalyomede\Rules\Performance\NoRedirects;
use Khalyomede\Rules\Performance\TextCompressionEnabled;
use Khalyomede\Rules\Performance\UsesHttp2;
use Khalyomede\Rules\Security\ServerHeaderHidden;
use Khalyomede\Rules\Security\StrictTransportSecurityHeaderPresent;
use Khalyomede\Rules\Security\XFrameOptionHeaderPresent;
use Khalyomede\Rules\Security\XPoweredByHidden;
use Khalyomede\Rules\Seo\LangPresent;
use Khalyomede\Rules\Seo\TitlePresent;

class Analyser
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function analyse(Page $page): Report
    {
        $start = microtime(true);

        $response = $this->getResponse($page);

        $report = new Report();

        $report->url($page->url())
            ->setRuleReports($this->ruleReports($response))
            ->durationInSeconds(round((microtime(true) - $start), 2));

        return $report;
    }

    /**
     * @return array<RuleReport>
     */
    private function ruleReports(Response $response): array
    {
        return [
            // Security
            XFrameOptionHeaderPresent::fromResponse($response)->toReport(),
            StrictTransportSecurityHeaderPresent::fromResponse($response)->toReport(),
            ServerHeaderHidden::fromResponse($response)->toReport(),
            XPoweredByHidden::fromResponse($response)->toReport(),
            // Seo
            TitlePresent::fromResponse($response)->toReport(),
            LangPresent::fromResponse($response)->toReport(),
            // Performance
            TextCompressionEnabled::fromResponse($response)->toReport(),
            NoRedirects::fromResponse($response)->toReport(),
            FastResponseTime::fromResponse($response)->toReport(),
            UsesHttp2::fromResponse($response)->toReport(),
        ];
    }

    private function getResponse(Page $page): Response
    {
        $responseTimeInSeconds = 0;

        $response = $this->client->request("GET", $page->url(), [
            "query" => $page->queries(),
            RequestOptions::ON_STATS => function (TransferStats $stats) use (&$responseTimeInSeconds): void {
                $responseTimeInSeconds = $stats->getTransferTime() ?? 2;
            },
        ]);

        return (new Response())
            ->setOriginalResponse($response)
            ->setResponseTimeInSeconds($responseTimeInSeconds);
    }
}
