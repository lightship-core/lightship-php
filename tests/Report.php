<?php

use Faker\Factory;
use Lightship\Report;

test("it can access durationInSeconds", function (): void {
    $faker = Factory::create();
    $durationInSeconds = $faker->randomFloat();

    $report = new Report(
        $faker->url(),
        $durationInSeconds,
        []
    );

    expect($durationInSeconds, $report->durationInSeconds);
});
