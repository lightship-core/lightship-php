<?php

namespace Khalyomede;

use GuzzleHttp\Client;
use Khalyomede\Rules\Performance\NoRedirects;
use Khalyomede\Rules\Performance\TextCompressionEnabled;
use Khalyomede\Rules\Security\ServerHeaderHidden;
use Khalyomede\Rules\Security\StrictTransportSecurityHeaderPresent;
use Khalyomede\Rules\Security\XFrameOptionHeaderPresent;
use Khalyomede\Rules\Security\XPoweredByHidden;
use Khalyomede\Rules\Seo\LangPresent;
use Khalyomede\Rules\Seo\TitlePresent;
use Psr\Http\Message\ResponseInterface;

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
    private function ruleReports(ResponseInterface $response): array
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
        ];
    }

    private function getResponse(Page $page): ResponseInterface
    {
        return $this->client->request("GET", $page->url(), [
            "query" => $page->queries(),
        ]);
    }
}
