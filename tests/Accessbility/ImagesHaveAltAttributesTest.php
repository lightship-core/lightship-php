<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Lightship\Lightship;
use Lightship\Rules\Accessibility\ImagesHaveAltAttributes;
use Lightship\RuleType;

test("images have alt attributes is an accessibility rule", function (): void {
    expect((new ImagesHaveAltAttributes())->type())->toBe(RuleType::Accessibility);
});

test("ids are unique passes if all images have an alt attribute filled", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<img alt='foo' /><img alt='bar' />")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["accessibility"][4])->toBe([
        "name" => "imagesHaveAltAttributes",
        "passes" => true,
    ]);
});

test("ids are unique passes if there is no images", function (): void {
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

    expect($data[0]["accessibility"][4])->toBe([
        "name" => "imagesHaveAltAttributes",
        "passes" => true,
    ]);
});

test("ids are unique does not passes if one of the images have an empty alt attribute", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<img alt='foo' /><img alt='' />")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["accessibility"][4])->toBe([
        "name" => "imagesHaveAltAttributes",
        "passes" => false,
    ]);
});

test("ids are unique does not passes if all images have an empty alt attribute", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<img alt='' /><img alt='' />")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["accessibility"][4])->toBe([
        "name" => "imagesHaveAltAttributes",
        "passes" => false,
    ]);
});

test("ids are unique does not passes if one of the images have no alt attribute", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<img alt='foo' /><img />")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["accessibility"][4])->toBe([
        "name" => "imagesHaveAltAttributes",
        "passes" => false,
    ]);
});

test("ids are unique does not passes if all images have no alt attribute", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<img /><img />")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["accessibility"][4])->toBe([
        "name" => "imagesHaveAltAttributes",
        "passes" => false,
    ]);
});
