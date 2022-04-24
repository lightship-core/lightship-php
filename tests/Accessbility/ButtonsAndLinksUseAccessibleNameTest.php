<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Khalyomede\Lightship;
use Khalyomede\Rules\Accessibility\ButtonsAndLinksUseAccessibleName;
use Khalyomede\RuleType;

test("buttons and links use accessible name is an accessibility rule", function (): void {
    expect((new ButtonsAndLinksUseAccessibleName())->type())->toBe(RuleType::Accessibility);
});

test("buttons and links use accessible name passes if all buttons and links have a name", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<body><a>bar</a><button>foo</button></body>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["accessibility"][2])->toBe([
        "name" => "buttonsAndLinksUseAccessibleName",
        "passes" => true,
    ]);
});

test("buttons and links use accessible name passes if all buttons and links have an aria label", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<body><a aria-label='foo'></a><button aria-label='bar'></button></body>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["accessibility"][2])->toBe([
        "name" => "buttonsAndLinksUseAccessibleName",
        "passes" => true,
    ]);
});

test("buttons and links use accessible name passes if all buttons and links have both a name and an aria label", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<body><a aria-label='foo'>foo</a><button aria-label='bar'>bar</button></body>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["accessibility"][2])->toBe([
        "name" => "buttonsAndLinksUseAccessibleName",
        "passes" => true,
    ]);
});

test("buttons and links use accessible name passes if there is no link nor buttons", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<body></body>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["accessibility"][2])->toBe([
        "name" => "buttonsAndLinksUseAccessibleName",
        "passes" => true,
    ]);
});

test("buttons and links use accessible name does not pass if a button does not have name or aria label", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<body><a aria-label='foo'></a><button></button></body>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["accessibility"][2])->toBe([
        "name" => "buttonsAndLinksUseAccessibleName",
        "passes" => false,
    ]);
});

test("buttons and links use accessible name does not pass if a link does not have name or aria label", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<body><a></a><button>bar</button></body>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["accessibility"][2])->toBe([
        "name" => "buttonsAndLinksUseAccessibleName",
        "passes" => false,
    ]);
});

test("buttons and links use accessible name does not pass if buttons and links do not have name or aria label", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<body><a></a><button></button></body>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["accessibility"][2])->toBe([
        "name" => "buttonsAndLinksUseAccessibleName",
        "passes" => false,
    ]);
});
