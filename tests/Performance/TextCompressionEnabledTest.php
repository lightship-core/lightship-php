<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Khalyomede\Lightship;

test("text compression enabled passes if the header is present and has gzip value", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, ["content-encoding" => "gzip"], "<html></html>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    expect($data[0]["performance"][0])->toBe([
        "name" => "textCompressionEnabled",
        "passes" => true,
    ]);
});

test("text compression enabled passes if the header is present and has deflate value", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, ["content-encoding" => "deflate"], "<html></html>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    expect($data[0]["performance"][0])->toBe([
        "name" => "textCompressionEnabled",
        "passes" => true,
    ]);
});

test("text compression enabled passes if the header is present and has br value", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, ["content-encoding" => "br"], "<html></html>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    expect($data[0]["performance"][0])->toBe([
        "name" => "textCompressionEnabled",
        "passes" => true,
    ]);
});

test("text compression enabled passes if the x-header is present and has gzip value", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, ["x-encoded-content-encoding" => "gzip"], "<html></html>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    expect($data[0]["performance"][0])->toBe([
        "name" => "textCompressionEnabled",
        "passes" => true,
    ]);
});

test("text compression enabled passes if the x-header is present and has deflate value", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, ["x-encoded-content-encoding" => "deflate"], "<html></html>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    expect($data[0]["performance"][0])->toBe([
        "name" => "textCompressionEnabled",
        "passes" => true,
    ]);
});

test("text compression enabled passes if the x-header is present and has br value", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, ["x-encoded-content-encoding" => "br"], "<html></html>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    expect($data[0]["performance"][0])->toBe([
        "name" => "textCompressionEnabled",
        "passes" => true,
    ]);
});

test("text compression enabled does not pass if the header is present and has an invalid value", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, ["content-encoding" => "foo"], "<html></html>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    expect($data[0]["performance"][0])->toBe([
        "name" => "textCompressionEnabled",
        "passes" => false,
    ]);
});

test("text compression enabled does not pass if the header is not present", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<html></html>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    expect($data[0]["performance"][0])->toBe([
        "name" => "textCompressionEnabled",
        "passes" => false,
    ]);
});
