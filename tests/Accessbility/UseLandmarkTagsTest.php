<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Lightship\Lightship;
use Lightship\Rules\Accessibility\MetaViewportPresent;
use Lightship\RuleType;

test("use landmark tags present is a seo rule", function (): void {
    expect((new MetaViewportPresent())->type())->toBe(RuleType::Accessibility);
});

test("use landmark tags present passes if header tag is present", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<header></header")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["accessibility"][1])->toBe([
        "name" => "useLandmarkTags",
        "passes" => true,
    ]);
});

test("use landmark tags present passes if main tag is present", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<main></main")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["accessibility"][1])->toBe([
        "name" => "useLandmarkTags",
        "passes" => true,
    ]);
});

test("use landmark tags present passes if footer tag is present", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<footer></footer")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["accessibility"][1])->toBe([
        "name" => "useLandmarkTags",
        "passes" => true,
    ]);
});

test("use landmark tags present passes if nav tag is present", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<nav></nav")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["accessibility"][1])->toBe([
        "name" => "useLandmarkTags",
        "passes" => true,
    ]);
});

test("use landmark tags present does not pass if no landmark tags is present", function (): void {
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

    expect($data[0]["accessibility"][1])->toBe([
        "name" => "useLandmarkTags",
        "passes" => false,
    ]);
});
