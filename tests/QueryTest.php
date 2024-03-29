<?php

use Faker\Factory;
use Lightship\Query;

test("can set key and value", function (): void {
    $faker = Factory::create();
    $key = $faker->text();
    $value = $faker->text();
    $query = (new Query())->setKey($key)->setValue($value);

    expect($query->key())->toBe(rawurlencode($key));
    expect($query->value())->toBe(rawurlencode($value));
});

test("throw exception wehn setting empty key", function (): void {
    expect(fn (): Query => (new Query())->setKey(""))->toThrow(InvalidArgumentException::class);
});

test("throw exception when setting empty value", function (): void {
    expect(fn (): Query => (new Query())->setValue(""))->toThrow(InvalidArgumentException::class);
});
