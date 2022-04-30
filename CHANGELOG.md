# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Breaked

- Calling `Report::results()` now returns an array of `Result` instead of an associative array. See the [README](README.md#3-set-a-response-callback) for examples of usage.

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
