<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use Khalyomede\Lightship;

test("no redirects passes if the response was not behind any redirects", function (): void {
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

    expect($data[0]["performance"][1])->toBe([
        "name" => "noRedirects",
        "passes" => true,
    ]);
});

test("no redirects does not pass if the response was behind a redirection", function (): void {
    $stack = HandlerStack::create(new MockHandler([
        new Response(302, ['Location' => 'https://example.com']),
        new Response(200, [], "<html></html>"),
    ]));

    $stack->push(Middleware::redirect());

    $client = new Client([
        RequestOptions::ALLOW_REDIRECTS => [
            'track_redirects' => true,
        ],
        "handler" => $stack->resolve(),
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["performance"][1])->toBe([
        "name" => "noRedirects",
        "passes" => false,
    ]);
});
