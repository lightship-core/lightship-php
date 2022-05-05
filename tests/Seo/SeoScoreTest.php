<?php

use Lightship\Rules\Seo\LangPresent;
use Lightship\Rules\Seo\LinksDefineHref;
use Lightship\Rules\Seo\MetaDescriptionPresent;
use Lightship\Rules\Seo\TitlePresent;

test("accessibility total score is 100", function (): void {
    $rules = [
        LangPresent::class,
        LinksDefineHref::class,
        MetaDescriptionPresent::class,
        TitlePresent::class,
    ];

    $total = array_sum(array_map(fn (string $class): int => (new $class())->value(), $rules));

    expect($total)->toBe(100);
});
