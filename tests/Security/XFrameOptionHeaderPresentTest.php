<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Khalyomede\Lightship;

test("x frame options passes if the header is deny", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, ["X-Frame-Options" => "DENY"], "<html></html>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    expect($data[0]["security"][0])->toBe([
        "name" => "xFrameOptionsPresent",
        "passes" => true,
    ]);
});

test("x frame options passes if the header is sameorigin", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, ["X-Frame-Options" => "SAMEORIGIN"], "<html></html>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    expect($data[0]["security"][0])->toBe([
        "name" => "xFrameOptionsPresent",
        "passes" => true,
    ]);
});

test("x frame options does not passes if the header use deprecated allow-from", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, ["X-Frame-Options" => "ALLOW-FROM news.google.com"], "<html></html>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    expect($data[0]["security"][0])->toBe([
        "name" => "xFrameOptionsPresent",
        "passes" => false,
    ]);
});

test("x frame options does not passes if the header is something else", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, ["X-Frame-Options" => "foo"], "<html></html>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    expect($data[0]["security"][0])->toBe([
        "name" => "xFrameOptionsPresent",
        "passes" => false,
    ]);
});

test("x frame options does not passes if the header is empty", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, ["X-Frame-Options" => "foo"], "<html></html>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    expect($data[0]["security"][0])->toBe([
        "name" => "xFrameOptionsPresent",
        "passes" => false,
    ]);
});

test("x frame options does not passes if the header is not present", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<html></html>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    expect($data[0]["security"][0])->toBe([
        "name" => "xFrameOptionsPresent",
        "passes" => false,
    ]);
});
