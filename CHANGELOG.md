# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.8.0] 2022-05-06

### Added

- New methods `Lightship::allRulesPassed()`, `Lightship::rulePassed()` and `Lightship::someRulesPassed()` ([#6](https://github.com/lightship-core/lightship-php/issues/6)).

## [0.7.1] 2022-05-05

## Fixed

- When all accessibility rules pass, the total is now 100 as expected ([#4](https://github.com/lightship-core/lightship-php/issues/4)).

## [0.7.0] 2022-05-04

### Added

- You can now access `$report->durationInSeconds`.

## [0.6.0] 2022-05-02

### Breaked

- This package now requires PHP >=8.1.0 ([#1](https://github.com/lightship-core/lightship-php/issues/1)).

## [0.5.0] 2022-05-01

### Breaked

- Return type of `Lightship::toArray()` is no longer `mixed` but an array of array of detailed key/value with their types.

## [0.4.0] 2022-05-01

### Breaked

- Calling `Report::results()` now returns an array of `Result` instead of an associative array. See the [README](README.md#3-set-a-response-callback) for examples of usage.
- Return type of `Report::toArray()` is no longer `mixed`, but an array of detailed key/value with their types.

## [0.3.0] 2022-04-26

### Added

- Calling `$lightship->analyse()` now allows to chain other methods (like `->toJson()`).
- We can now set a custom GuzzleClient by calling `->client()`.

## [0.2.0] 2022-04-26

### Added

- End of file return lines to generated JSON report files.

### Fixed

- Loading a complete URL (starting with "http") will not use the previously used `->domain("foo")`.
- Specifying query strings will correctly be taken into account.
- All classes private properties have been turned into protected.

## [0.1.0] - 2022-04-24

### Added

- First working version.
