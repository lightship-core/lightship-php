<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Khalyomede\Lightship;
use Khalyomede\Rules\Seo\LangPresent;
use Khalyomede\RuleType;

test("langPresent is a seo rule", function (): void {
    expect((new LangPresent())->ruleType())->toBe(RuleType::Seo);
});

test("lang present passes if the lang is defined in the html tag", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<html lang='fr'></html>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["seo"][1])->toBe([
        "name" => "langPresent",
        "passes" => true,
    ]);
});

test("lang present does not pass if the lang is not defined in the html tag", function (): void {
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

    expect($data[0]["seo"][1])->toBe([
        "name" => "langPresent",
        "passes" => false,
    ]);
});

test("lang present does not pass if the lang is empty in the html tag", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<html lang=''></html>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["seo"][1])->toBe([
        "name" => "langPresent",
        "passes" => false,
    ]);
});

test("lang present does not pass if no html tag present", function (): void {
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

    expect($data[0]["seo"][1])->toBe([
        "name" => "langPresent",
        "passes" => false,
    ]);
});
