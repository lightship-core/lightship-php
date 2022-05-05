<?php

use Lightship\Rules\Security\ServerHeaderHidden;
use Lightship\Rules\Security\StrictTransportSecurityHeaderPresent;
use Lightship\Rules\Security\XFrameOptionHeaderPresent;
use Lightship\Rules\Security\XPoweredByHidden;

test("accessibility total score is 100", function (): void {
    $rules = [
        ServerHeaderHidden::class,
        StrictTransportSecurityHeaderPresent::class,
        XFrameOptionHeaderPresent::class,
        XPoweredByHidden::class,
    ];

    $total = array_sum(array_map(fn (string $class): int => (new $class())->value(), $rules));

    expect($total)->toBe(100);
});
