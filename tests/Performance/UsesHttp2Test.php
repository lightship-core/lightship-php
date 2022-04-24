<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Lightship\Lightship;

test("test uses http2 passes if the protocol is 2", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<html></html>", "2")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["performance"][3])->toBe([
        "name" => "usesHttp2",
        "passes" => true,
    ]);
});

test("test uses http2 passes if the protocol is 2.0", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<html></html>", "2.0")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["performance"][3])->toBe([
        "name" => "usesHttp2",
        "passes" => true,
    ]);
});

test("test uses http2 does not pass if the protocol is 1.1", function (): void {
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

    expect($data[0]["performance"][3])->toBe([
        "name" => "usesHttp2",
        "passes" => false,
    ]);
});
