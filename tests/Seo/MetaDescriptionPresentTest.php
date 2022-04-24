<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Lightship\Lightship;
use Lightship\Rules\Seo\MetaDescriptionPresent;
use Lightship\RuleType;

test("meta description present is a seo rule", function (): void {
    expect((new MetaDescriptionPresent())->type())->toBe(RuleType::Seo);
});

test("meta description present passes if the tag is present and filled", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<meta name='description' content='foo' />")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["seo"][3])->toBe([
        "name" => "metaDescriptionPresent",
        "passes" => true,
    ]);
});

test("meta description present passes if there is no tag", function (): void {
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

    expect($data[0]["seo"][3])->toBe([
        "name" => "metaDescriptionPresent",
        "passes" => true,
    ]);
});

test("meta description present does not pass if the tag is present and has empty content", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<meta name='description' content='' />")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["seo"][3])->toBe([
        "name" => "metaDescriptionPresent",
        "passes" => false,
    ]);
});

test("meta description present does not pass if the tag is present and has no content", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<meta name='description' />")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["seo"][3])->toBe([
        "name" => "metaDescriptionPresent",
        "passes" => false,
    ]);
});

test("meta description present does not pass if the tag is present and has no description attribute", function (): void {
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

    expect($data[0]["seo"][3])->toBe([
        "name" => "metaDescriptionPresent",
        "passes" => false,
    ]);
});

test("meta description present does not pass if the tag is present and has no name attribute", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<meta />")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["seo"][3])->toBe([
        "name" => "metaDescriptionPresent",
        "passes" => false,
    ]);
});
