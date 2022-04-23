<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Khalyomede\Lightship;
use Khalyomede\Rules\Security\ServerHeaderHidden;
use Khalyomede\RuleType;

test("server header hidden is a security rule", function (): void {
    expect((new ServerHeaderHidden())->ruleType())->toBe(RuleType::Security);
});

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

    assert(is_array($data[0]));

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

    assert(is_array($data[0]));

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

    assert(is_array($data[0]));

    expect($data[0]["security"][2])->toBe([
        "name" => "serverHeaderHidden",
        "passes" => false,
    ]);
});
