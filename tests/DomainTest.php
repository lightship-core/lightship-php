<?php

use Faker\Factory;
use Khalyomede\Domain;
use Khalyomede\Query;
use Khalyomede\Route;

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
    $secondRoutePath = $faker->url();

    $domain->setRoutes([
        (new Route())
            ->setPath($firstRoutePath)
            ->setQueries([
                (new Query())
                    ->setKey($faker->text())
                    ->setValue($faker->text())
            ]),
        (new Route())
            ->setPath($secondRoutePath),
    ]);

    $routes = $domain->routes();

    expect($routes[0]->path() === $firstRoutePath);
    expect($routes[1]->path() === $firstRoutePath);
});

test("setRoutes returns instance of Domain", function (): void {
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

    expect(fn () => $domain->setRoutes([$faker->text()]))->toThrow(TypeError::class);
});