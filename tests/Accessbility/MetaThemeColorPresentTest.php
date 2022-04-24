<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Khalyomede\Lightship;
use Khalyomede\Rules\Accessibility\MetaThemeColorPresent;
use Khalyomede\RuleType;

test("meta theme color is an accessibility rule", function (): void {
    expect((new MetaThemeColorPresent())->type())->toBe(RuleType::Accessibility);
});

test("meta theme color passes if there is a meta tag with a content", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<head><meta name='theme-color' content='#4285f4' /></head>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["accessibility"][6])->toBe([
        "name" => "metaThemeColorPresent",
        "passes" => true,
    ]);
});

test("meta theme color does not passes if there is a meta tag with an empty content", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<head><meta name='theme-color' content='' /></head>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["accessibility"][6])->toBe([
        "name" => "metaThemeColorPresent",
        "passes" => false,
    ]);
});

test("meta theme color does not passes if there is no meta tag theme-color", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<head><meta name='description' content='foo' /></head>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["accessibility"][6])->toBe([
        "name" => "metaThemeColorPresent",
        "passes" => false,
    ]);
});
