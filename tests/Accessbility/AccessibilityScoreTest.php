<?php

use Lightship\Rules\Accessibility\ButtonsAndLinksUseAccessibleName;
use Lightship\Rules\Accessibility\DoctypeHtmlPresent;
use Lightship\Rules\Accessibility\IdsAreUnique;
use Lightship\Rules\Accessibility\ImagesHaveAltAttributes;
use Lightship\Rules\Accessibility\MetaThemeColorPresent;
use Lightship\Rules\Accessibility\MetaViewportPresent;
use Lightship\Rules\Accessibility\UseLandmarkTags;

test("accessibility total score is 100", function (): void {
    $rules = [
        ButtonsAndLinksUseAccessibleName::class,
        DoctypeHtmlPresent::class,
        IdsAreUnique::class,
        ImagesHaveAltAttributes::class,
        MetaThemeColorPresent::class,
        MetaViewportPresent::class,
        UseLandmarkTags::class,
    ];

    $total = array_sum(array_map(fn (string $class): int => (new $class())->value(), $rules));

    expect($total)->toBe(100);
});
