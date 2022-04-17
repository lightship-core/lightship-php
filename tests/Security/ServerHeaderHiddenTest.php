<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Khalyomede\Lightship;

test("server header hidden passes if the header is not present", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<html></html>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    expect($data[0]["security"][2])->toBe([
        "name" => "serverHeaderHidden",
        "passes" => true,
    ]);
});

test("server header hidden passes if the header is empty", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, ["Server" => ""], "<html></html>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    expect($data[0]["security"][2])->toBe([
        "name" => "serverHeaderHidden",
        "passes" => true,
    ]);
});

test("server header hidden does not pass if the header is filled", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, ["Server" => "Ubuntu 20.04/Apache 2.4"], "<html></html>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    expect($data[0]["security"][2])->toBe([
        "name" => "serverHeaderHidden",
        "passes" => false,
    ]);
});
