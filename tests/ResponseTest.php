<?php

use Faker\Factory;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Lightship\Response;

test("returns status code", function (): void {
    $faker = Factory::create();
    $contentType = "application/json";
    $guzzleResponse = new GuzzleResponse(200, ["Content-Type" => $contentType]);
    $responseTime = $faker->randomFloat(2);

    $response = new Response($guzzleResponse, $responseTime);

    expect($response->getStatusCode())->toBe(200);
    expect($response->hasHeader("Content-Type"))->toBeTrue();
    expect($response->hasHeader("Content-Encoding"))->toBeFalse();
    expect($response->getHeader("Content-Type"))->toBe([$contentType]);
});
