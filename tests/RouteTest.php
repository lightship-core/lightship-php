<?php

use Faker\Factory;
use Lightship\Query;
use Lightship\Route;

test("set route and queries", function (): void {
    $faker = Factory::create();
    $path = $faker->url();
    $firstQueryKey = $faker->text();
    $firstQueryValue = $faker->text();
    $secondQueryKey = $faker->text();
    $secondQueryValue = $faker->text();
    $route = (new Route())
        ->setPath($path)
        ->setQueries([
            (new Query())
                ->setKey($firstQueryKey)
                ->setValue($firstQueryValue),
            (new Query())
                ->setKey($secondQueryKey)
                ->setValue($secondQueryValue)
        ]);

    expect($route->path())->toBe($path . "?" . http_build_query([
        rawurlencode($firstQueryKey) => rawurlencode($firstQueryValue),
        rawurlencode($secondQueryKey) => rawurlencode($secondQueryValue),
    ]));
    expect($route->queries()[0]->key())->toBe(rawurlencode($firstQueryKey));
    expect($route->queries()[0]->value())->toBe(rawurlencode($firstQueryValue));
    expect($route->queries()[1]->key())->toBe(rawurlencode($secondQueryKey));
    expect($route->queries()[1]->value())->toBe(rawurlencode($secondQueryValue));
});

test("throws exception when setting empty path", function (): void {
    expect(fn (): Route => (new Route())->setPath(""))->toThrow(InvalidArgumentException::class);
});

test("queries list returns queries formatted for Guzzle", function (): void {
    $faker = Factory::create();
    $query1Key = $faker->text();
    $query1Value = $faker->text();
    $query2Key = $faker->text();
    $query2Value = $faker->text();

    $route = (new Route())
        ->setQueries([
            (new Query())->setKey($query1Key)->setValue($query1Value),
            (new Query())->setKey($query2Key)->setValue($query2Value),
        ]);

    expect($route->queriesList())->toBe([
        rawurlencode($query1Key) => rawurlencode($query1Value),
        rawurlencode($query2Key) => rawurlencode($query2Value),
    ]);
});
