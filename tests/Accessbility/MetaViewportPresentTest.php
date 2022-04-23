<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Khalyomede\Lightship;
use Khalyomede\Rules\Accessibility\MetaViewportPresent;
use Khalyomede\RuleType;

test("meta viewport present is a seo rule", function (): void {
    expect((new MetaViewportPresent())->type())->toBe(RuleType::Accessibility);
});

test("meta viewport present passes if the tag is present and starts with width", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<meta name='viewport' content='width=device-width, initial-scale=1' />")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["accessibility"][0])->toBe([
        "name" => "metaViewportPresent",
        "passes" => true,
    ]);
});

test("meta viewport present passes if the tag is present and starts with initial-scale", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<meta name='viewport' content='initial-scale=1, width=device-width' />")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["accessibility"][0])->toBe([
        "name" => "metaViewportPresent",
        "passes" => true,
    ]);
});

test("meta viewport present pass if there is no meta tags", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<!DOCTYPE html>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["accessibility"][0])->toBe([
        "name" => "metaViewportPresent",
        "passes" => true,
    ]);
});

test("meta viewport present does not pass if the tag is present and has empty content", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<meta name='viewport' content='' />")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["accessibility"][0])->toBe([
        "name" => "metaViewportPresent",
        "passes" => false,
    ]);
});

test("meta viewport present does not pass if the tag is present and has no content", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<meta name='viewport' />")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["accessibility"][0])->toBe([
        "name" => "metaViewportPresent",
        "passes" => false,
    ]);
});

test("meta viewport present does not pass if the tag is present and has no description attribute", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<meta name='foo' />")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["accessibility"][0])->toBe([
        "name" => "metaViewportPresent",
        "passes" => false,
    ]);
});

test("meta viewport present does not pass if the tag is present and has no name attribute", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<meta> />")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["accessibility"][0])->toBe([
        "name" => "metaViewportPresent",
        "passes" => false,
    ]);
});
