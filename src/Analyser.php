<?php

namespace Lightship;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\TransferStats;
use Lightship\Rules\Accessibility\ButtonsAndLinksUseAccessibleName;
use Lightship\Rules\Accessibility\DoctypeHtmlPresent;
use Lightship\Rules\Accessibility\IdsAreUnique;
use Lightship\Rules\Accessibility\ImagesHaveAltAttributes;
use Lightship\Rules\Accessibility\MetaThemeColorPresent;
use Lightship\Rules\Accessibility\MetaViewportPresent;
use Lightship\Rules\Accessibility\UseLandmarkTags;
use Lightship\Rules\Performance\FastResponseTime;
use Lightship\Rules\Performance\NoRedirects;
use Lightship\Rules\Performance\TextCompressionEnabled;
use Lightship\Rules\Performance\UsesHttp2;
use Lightship\Rules\Security\ServerHeaderHidden;
use Lightship\Rules\Security\StrictTransportSecurityHeaderPresent;
use Lightship\Rules\Security\XFrameOptionHeaderPresent;
use Lightship\Rules\Security\XPoweredByHidden;
use Lightship\Rules\Seo\LangPresent;
use Lightship\Rules\Seo\LinksDefineHref;
use Lightship\Rules\Seo\MetaDescriptionPresent;
use Lightship\Rules\Seo\TitlePresent;

class Analyser
{
    protected Client $client;

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
    protected function ruleReports(Response $response): array
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
            IdsAreUnique::fromResponse($response)->toReport(),
            ImagesHaveAltAttributes::fromResponse($response)->toReport(),
            DoctypeHtmlPresent::fromResponse($response)->toReport(),
            MetaThemeColorPresent::fromResponse($response)->toReport(),
        ];
    }

    protected function getResponse(Route $route): Response
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
