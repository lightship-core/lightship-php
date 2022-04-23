<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Khalyomede\Lightship;
use Khalyomede\Rules\Accessibility\IdsAreUnique;
use Khalyomede\RuleType;

test("ids are unique is an accessibility rule", function (): void {
    expect((new IdsAreUnique())->type())->toBe(RuleType::Accessibility);
});

test("ids are unique passes if there is no duplicated ids", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<body><a id='foo'>foo</a><button id='bar'>bar</button></body>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["accessibility"][3])->toBe([
        "name" => "idsAreUnique",
        "passes" => true,
    ]);
});

test("ids are unique passes if there is no ids", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<body><a>foo</a><button>bar</button></body>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["accessibility"][3])->toBe([
        "name" => "idsAreUnique",
        "passes" => true,
    ]);
});

test("ids are unique passes if there is duplicated ids", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<body><a id='foo'>foo</a><button id='foo'>bar</button></body>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["accessibility"][3])->toBe([
        "name" => "idsAreUnique",
        "passes" => false,
    ]);
});
