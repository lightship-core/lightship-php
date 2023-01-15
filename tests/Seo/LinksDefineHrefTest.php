<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Lightship\Lightship;
use Lightship\Rules\Seo\LinksDefineHref;
use Lightship\RuleType;

test("links define href is a seo rule", function (): void {
    expect((new LinksDefineHref())->type())->toBe(RuleType::Seo);
});

test("links define href pass if all links have non empty href", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<a href='foo'></a>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["seo"][2])->toBe([
        "name" => "linksDefineHref",
        "passes" => true,
    ]);
});

test("links define href pass if all links have empty href", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<a href=''></a>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["seo"][2])->toBe([
        "name" => "linksDefineHref",
        "passes" => false,
    ]);
});

test("links define href pass if all links have no href", function (): void {
    $client = new Client([
        "handler" => HandlerStack::create(new MockHandler([
            new Response(200, [], "<a></a>")
        ]))
    ]);

    $lightship = new Lightship($client);

    $lightship->route("https://example.com")
        ->analyse();

    $data = $lightship->toArray();

    assert(is_array($data[0]));

    expect($data[0]["seo"][2])->toBe([
        "name" => "linksDefineHref",
        "passes" => false,
    ]);
});
