<?php

use Faker\Factory;
use Khalyomede\Query;
use Khalyomede\Route;

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

    expect($route->path())->toBe($path);
    expect($route->queries()[0]->key())->toBe(rawurlencode($firstQueryKey));
    expect($route->queries()[0]->value())->toBe(rawurlencode($firstQueryValue));
    expect($route->queries()[1]->key())->toBe(rawurlencode($secondQueryKey));
    expect($route->queries()[1]->value())->toBe(rawurlencode($secondQueryValue));
});

test("throws exception when setting empty path", function (): void {
    expect(fn () => (new Route())->setPath(""))->toThrow(InvalidArgumentException::class);
});
