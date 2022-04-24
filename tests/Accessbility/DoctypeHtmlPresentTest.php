<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Lightship\Lightship;
use Lightship\Rules\Accessibility\DoctypeHtmlPresent;
use Lightship\RuleType;

test("doctype html present is an accessibility rule", function (): void {
    expect((new DoctypeHtmlPresent())->type())->toBe(RuleType::Accessibility);
});

test("doctype html passes if the document starts with DOCTYPE", function (): void {
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

    expect($data[0]["accessibility"][5])->toBe([
        "name" => "doctypeHtmlPresent",
        "passes" => true,
    ]);
});

test("doctype html does not passes if the document does not start with DOCTYPE", function (): void {
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

    expect($data[0]["accessibility"][5])->toBe([
        "name" => "doctypeHtmlPresent",
        "passes" => false,
    ]);
});

test("doctype html does not passes if the document is empty", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["accessibility"][5])->toBe([
        "name" => "doctypeHtmlPresent",
        "passes" => false,
    ]);
});
