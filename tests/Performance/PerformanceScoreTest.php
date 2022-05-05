<?php

use Lightship\Rules\Performance\FastResponseTime;
use Lightship\Rules\Performance\NoRedirects;
use Lightship\Rules\Performance\TextCompressionEnabled;
use Lightship\Rules\Performance\UsesHttp2;

test("accessibility total score is 100", function (): void {
    $rules = [
        FastResponseTime::class,
        NoRedirects::class,
        TextCompressionEnabled::class,
        UsesHttp2::class,
    ];

    $total = array_sum(array_map(fn (string $class): int => (new $class())->value(), $rules));

    expect($total)->toBe(100);
});
