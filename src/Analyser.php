<?php

namespace Khalyomede;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\TransferStats;
use Khalyomede\Rules\Accessibility\ButtonsAndLinksUseAccessibleName;
use Khalyomede\Rules\Accessibility\MetaViewportPresent;
use Khalyomede\Rules\Accessibility\UseLandmarkTags;
use Khalyomede\Rules\Performance\FastResponseTime;
use Khalyomede\Rules\Performance\NoRedirects;
use Khalyomede\Rules\Performance\TextCompressionEnabled;
use Khalyomede\Rules\Performance\UsesHttp2;
use Khalyomede\Rules\Security\ServerHeaderHidden;
use Khalyomede\Rules\Security\StrictTransportSecurityHeaderPresent;
use Khalyomede\Rules\Security\XFrameOptionHeaderPresent;
use Khalyomede\Rules\Security\XPoweredByHidden;
use Khalyomede\Rules\Seo\LangPresent;
use Khalyomede\Rules\Seo\LinksDefineHref;
use Khalyomede\Rules\Seo\MetaDescriptionPresent;
use Khalyomede\Rules\Seo\TitlePresent;

class Analyser
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function analyse(Route $route): Report
    {
        $start = microtime(true);

        $response = $this->getResponse($route);

        $report = new Report();

        $report->url($route->path())
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
            LinksDefineHref::fromResponse($response)->toReport(),
            MetaDescriptionPresent::fromResponse($response)->toReport(),
            // Performance
            TextCompressionEnabled::fromResponse($response)->toReport(),
            NoRedirects::fromResponse($response)->toReport(),
            FastResponseTime::fromResponse($response)->toReport(),
            UsesHttp2::fromResponse($response)->toReport(),
            // Accessibility
            MetaViewportPresent::fromResponse($response)->toReport(),
            UseLandmarkTags::fromResponse($response)->toReport(),
            ButtonsAndLinksUseAccessibleName::fromResponse($response)->toReport(),
        ];
    }

    private function getResponse(Route $route): Response
    {
        $responseTimeInSeconds = 0;

        $response = $this->client->request("GET", $route->path(), [
            "query" => $route->queriesList(),
            RequestOptions::ON_STATS => function (TransferStats $stats) use (&$responseTimeInSeconds): void {
                $responseTimeInSeconds = $stats->getTransferTime() ?? 2;
            },
        ]);

        return (new Response())
            ->setOriginalResponse($response)
            ->setResponseTimeInSeconds($responseTimeInSeconds);
    }
}
