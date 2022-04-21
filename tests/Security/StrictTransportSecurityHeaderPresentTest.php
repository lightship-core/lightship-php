<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Khalyomede\Lightship;

test("strict transport security header present passes if the header has a max-age value", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, ["Strict-Transport-Security" => "max-age=31536000"], "<html></html>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["security"][1])->toBe([
        "name" => "strictTransportSecurityHeaderPresent",
        "passes" => true,
    ]);
});

test("strict transport security header present passes if the header has a max-age value with a includeSubdomains", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, ["Strict-Transport-Security" => "max-age=31536000; includeSubdomains"], "<html></html>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["security"][1])->toBe([
        "name" => "strictTransportSecurityHeaderPresent",
        "passes" => true,
    ]);
});

test("strict transport security header present passes if the header has a max-age value with a includeSubdomains preload", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, ["Strict-Transport-Security" => "max-age=31536000; includeSubdomains: preload"], "<html></html>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["security"][1])->toBe([
        "name" => "strictTransportSecurityHeaderPresent",
        "passes" => true,
    ]);
});

test("strict transport security header present does not pass if the header is empty", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, ["Strict-Transport-Security" => ""], "<html></html>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["security"][1])->toBe([
        "name" => "strictTransportSecurityHeaderPresent",
        "passes" => false,
    ]);
});

test("strict transport security header present does not pass if the header is not present", function (): void {
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

    expect($data[0]["security"][1])->toBe([
        "name" => "strictTransportSecurityHeaderPresent",
        "passes" => false,
    ]);
});
