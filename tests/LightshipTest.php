<?php

use Faker\Factory;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Lightship\Lightship;
use Lightship\Report;
use Lightship\Route;
use Lightship\Rules\Accessibility\IdsAreUnique;
use Lightship\Rules\Performance\FastResponseTime;
use Lightship\Rules\Seo\LangPresent;
use Lightship\RuleType;

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

    expect($content)->toBe("$route1: 0/50/50/44\n$route2: 0/50/50/44\n");
});

test("it can generate json report", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], ""),
            new Response(200, [], ""),
        ]))
    ]);

    $lighship = new Lightship($client);

    $lighship
        ->domain("https://example.com")
        ->route("/contact-us/", ["theme" => "dark"])
        ->route("https://example.com/", ["lang" => "en"])
        ->analyse();

    expect($lighship->toPrettyJson())->toBe(file_get_contents(__DIR__ . "/misc/report.json"));
});

test("it can use lightship.json", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], ""),
            new Response(200, [], ""),
        ]))
    ]);

    $lighship = new Lightship($client);

    $lighship->config(__DIR__ . "/misc/lightship.json")
        ->analyse();

    expect($lighship->toPrettyJson())->toBe(file_get_contents(__DIR__ . "/misc/report.json"));
});

test("it throws an exception when loading a config file that do not exist", function (): void {
    $faker = Factory::create();

    expect(fn () => (new Lightship())->config(__DIR__ . "/foo.json"))->toThrow(Exception::class, "File does not exist");
});


test("it throws an exception when loading a config file that is not a correct JSON", function (): void {
    $faker = Factory::create();

    expect(fn () => (new Lightship())->config(__DIR__ . "/misc/bad-lightship.json"))->toThrow(Exception::class, "Could not read config file");
});

test("can chain methods after calling analyse()", function (): void {
    $faker = Factory::create();
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], ""),
        ]))
    ]);

    $lighship = new Lightship($client);

    expect($lighship->route($faker->url())->analyse())->toBeInstanceOf(Lightship::class);
});

test("can add a custom Guzzle client by calling ->client() and we can chain methods after", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], ""),
        ]))
    ]);

    expect((new Lightship())->client($client))->toBeInstanceOf(Lightship::class);
});

test("it can assert an URL pass all rules", function (): void {
    $faker = Factory::create();
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], ""),
        ]))
    ]);

    $url = $faker->url();

    $lighship = new Lightship($client);

    $lighship->route($url)
        ->analyse();

    expect($lighship->allRulesPassed([$url]))->toBeFalse();
});

test("it can assert a specific rule passed for an URL", function (): void {
    $faker = Factory::create();
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], ""),
        ]))
    ]);

    $url = $faker->url();

    $lighship = new Lightship($client);

    $lighship->route($url)
        ->analyse();

    expect($lighship->rulePassed([$url], FastResponseTime::class))->toBeTrue();
});

test("it asserts a specific rule did not passed for an URL", function (): void {
    $faker = Factory::create();
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], ""),
        ]))
    ]);

    $url = $faker->url();

    $lighship = new Lightship($client);

    $lighship->route($url)
        ->analyse();

    expect($lighship->rulePassed([$url], LangPresent::class))->toBeFalse();
});

test("it can assert some rules passed for an URL", function (): void {
    $faker = Factory::create();
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], ""),
        ]))
    ]);

    $url = $faker->url();

    $lighship = new Lightship($client);

    $lighship->route($url)
        ->analyse();

    expect($lighship->someRulesPassed([$url], [FastResponseTime::class, IdsAreUnique::class]))->toBeTrue();
});

test("it does not assert some rules passed for an URL if at least one did not pass", function (): void {
    $faker = Factory::create();
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], ""),
        ]))
    ]);

    $url = $faker->url();

    $lighship = new Lightship($client);

    $lighship->route($url)
        ->analyse();

    expect($lighship->someRulesPassed([$url], [FastResponseTime::class, LangPresent::class]))->toBeFalse();
});
