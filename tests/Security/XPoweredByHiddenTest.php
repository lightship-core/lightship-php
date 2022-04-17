<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Khalyomede\Lightship;

test("x powered by passes if the header is not present", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<html></html>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    expect($data[0]["security"][3])->toBe([
        "name" => "xPoweredByHidden",
        "passes" => true,
    ]);
});

test("x powered by passes if the header is empty", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, ["X-Powered-By" => ""], "<html></html>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    expect($data[0]["security"][3])->toBe([
        "name" => "xPoweredByHidden",
        "passes" => true,
    ]);
});

test("x powered by does not pass if the header is filled", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, ["X-Powered-By" => "PHP 7.4"], "<html></html>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    expect($data[0]["security"][3])->toBe([
        "name" => "xPoweredByHidden",
        "passes" => false,
    ]);
});
