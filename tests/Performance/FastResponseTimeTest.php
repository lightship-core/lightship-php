<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Khalyomede\Lightship;
use Psr\Http\Message\RequestInterface;

test("fast response time passes if the server responded in <= 1s", function (): void {
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

    expect($data[0]["performance"][2])->toBe([
        "name" => "fastResponseTime",
        "passes" => true,
    ]);
});

test("fast response time does not pass if the server responded in more than 1s", function (): void {
    $stack = HandlerStack::create(new MockHandler([
        new Response(200, [], "<html></html>")
    ]));

    $stack->push(
        fn (callable $handler): callable =>
        function (RequestInterface $request, array $options) use ($handler) {
            sleep(2);

            return $handler($request, $options);
        }
    );

    $client = new Client([
        "handler" => $stack,
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["performance"][2])->toBe([
        "name" => "fastResponseTime",
        "passes" => false,
    ]);
})->skip("Find how to simulate latency > 1s");
