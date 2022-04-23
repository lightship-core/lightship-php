<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Khalyomede\Lightship;
use Khalyomede\Rules\Seo\TitlePresent;
use Khalyomede\RuleType;

test("titlePresent is a seo rule", function (): void {
    expect((new TitlePresent())->type())->toBe(RuleType::Seo);
});

test('test titlePresent does pass when the title tag is filled', function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<title>Foo</title>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["seo"][0])->toBe([
        "name" => "titlePresent",
        "passes" => true,
    ]);
});

test('test titlePresent does not pass when the title tag is missing', function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<html></html>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["seo"][0])->toBe([
        "name" => "titlePresent",
        "passes" => false,
    ]);
});

test('test titlePresent does not pass when the title tag is empty', function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<title></title>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["seo"][0])->toBe([
        "name" => "titlePresent",
        "passes" => false,
    ]);
});
