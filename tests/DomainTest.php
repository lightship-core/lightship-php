<?php

use Faker\Factory;
use Lightship\Domain;
use Lightship\Query;
use Lightship\Route;

test("can set base", function (): void {
    $faker = Factory::create();
    $domain = new Domain();
    $base = $faker->url();

    expect($domain->setBase($base)->base())->toBe($base);
});

test("can set multiple routes", function (): void {
    $faker = Factory::create();
    $domain = new Domain();

    $firstRoutePath = $faker->url();
    $firstRouteQueryKey = $faker->text();
    $firstRouteQueryValue = $faker->text();
    $secondRoutePath = $faker->url();

    $domain->setRoutes([
        (new Route())
            ->setPath($firstRoutePath)
            ->setQueries([
                (new Query())
                    ->setKey($firstRouteQueryKey)
                    ->setValue($firstRouteQueryValue)
            ]),
        (new Route())
            ->setPath($secondRoutePath),
    ]);

    $routes = $domain->routes();

    expect($routes[0]->path())->toBe($firstRoutePath . "?" . http_build_query([rawurlencode($firstRouteQueryKey) => rawurlencode($firstRouteQueryValue)]));
    expect($routes[1]->path())->toBe($secondRoutePath);
});

test("setRoutes returns instance of Domain", function (): void {
    /**
     * @phpstan-ignore-next-line Parameter #1 $routes of method Khalyomede\Domain::setRoutes() expects array<Khalyomede\Route>, array<int, string> given.
     */
    expect((new Domain())->setRoutes([]))->toBeInstanceOf(Domain::class);
});

test("addRoute add the Route", function (): void {
    $faker = Factory::create();
    $path = $faker->url();
    $domain = (new Domain())->setRoutes([(new Route())->setPath($path)]);

    expect($domain->routes()[0]->path())->toBe($path);
});

test("throw exception when setting a route that is not instanceof Route", function (): void {
    $faker = Factory::create();
    $domain = (new Domain());

    /** @phpstan-ignore-next-line Parameter #1 $routes of method Khalyomede\Domain::setRoutes() expects array<Khalyomede\Route>, array<int, string> given. */
    expect(fn (): Domain => $domain->setRoutes([$faker->text()]))->toThrow(TypeError::class);
});
