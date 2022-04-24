<?php

use Faker\Factory;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Khalyomede\Lightship;
use Khalyomede\Report;
use Khalyomede\Route;
use Khalyomede\RuleType;

test("it can set report callback", function (): void {
    $faker = Factory::create();
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], ""),
            new Response(200, [], ""),
        ]))
    ]);

    $route1 = $faker->url();
    $route2 = $faker->url();

    $lighship = new Lightship($client);

    ob_start();

    $lighship->route($route1)
        ->route($route2)
        ->onReportedRoute(function (Route $route, Report $report): void {
            echo "{$route->path()}: {$report->score(RuleType::Seo)}/{$report->score(RuleType::Security)}/{$report->score(RuleType::Performance)}/{$report->score(RuleType::Accessibility)}" . PHP_EOL;
        })
        ->analyse();

    $content = ob_get_clean();

    expect($content)->toBe("$route1: 0/50/50/38\n$route2: 0/50/50/38\n");
});
