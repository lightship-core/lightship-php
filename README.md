# Lightship

Web page performance/seo/security/accessibility analysis, browser-less.

```json
[
  {
    "url": "https:\/\/example.com\/",
    "durationInSeconds": 0.15,
    "scores": {
      "seo": 100,
      "security": 75
    },
    "seo": [
      {
        "name": "titlePresent",
        "passes": true
      },
      {
        "name": "langPresent",
        "passes": true
      }
    ],
    "security": [
      {
        "name": "xFrameOptionsPresent",
        "passes": true
      },
      {
        "name": "strictTransportSecurityHeaderPresent",
        "passes": true
      },
      {
        "name": "serverHeaderHidden",
        "passes": false
      },
      {
        "name": "xPoweredByHidden",
        "passes": true
      }
    ]
  }
]
```

## Summary

- [About](#about)
- [Examples](#examples)
- [Tests](#tests)
- [Compatibility table](#compatibility-table)

## About

I made a web app, and I want to be able to frequently test my public facing pages respond to various criterias to optimize their referencing.

Since my web app is fully server-side, all my tests are very fast, and do not rely on a web browser. I wanted to stick to this, and have a fast tool to get simple insights, like not ommiting the alt attributes on images, to ensure my description and title are filled, or to evaluate aproximatively my page load time.

## Examples

- [1. Simple example code-driver](#1-simple-example-code-driven)
- [2. Simple example using a configuration file](#2-simple-example-using-a-configuration-file)
- []()

### 1. Simple example code-driven

In this example, we will configure our web pages using the code.

```php
require __DIR__ . "/vendor/autoload.php";

use Lightship\Lightship;

$lightship = new Lightship();

$lightship->domain("https://news.google.com")
  ->route("/foryou")
  ->route("/topstories")
  ->route("/my/searches", ["hl" => "fr", "gl" => "FR"]);
  ->analyse();

file_put_contents("report.json", $lightship->toPrettyJson());
```

### 2. Simple example using a configuration file

In this example, we will tell Lightship to use our "lightship.json" file instead of configuring it on the code.

```php
require __DIR__ . "/vendor/autoload.php";

use Lightship\Lightship;

$lightship = new Lightship();

$lightship->config(__DIR__ . "/lightship.json");
  ->analyse();

file_put_contents("report.json", $lightship->toPrettyJson());
```

And here is our configuration file.

```json
{
  "domains": [
    {
      "base": "https://news.google.com",
      "routes": [
        {
          "path": "/foryou"
        },
        {
          "path": "/topstories"
        },
        {
          "path": "/my/searches",
          "queries": [
            {
              "key": "hl",
              "value": "fr"
            },
            {
              "key": "gl",
              "value": "FR"
            }
          ]
        }
      ]
    }
  ]
}
```

### 3. Set a response callback

In this example, we will trigger our function after a response has been parsed and a report generated. Useful for cli programs.

```php
use Lightship\Lightship;

require __DIR__ . "/vendor/autoload.php";

$lightship = new Lightship();

$lightship->route("https://example.com")
  ->route("https://northerwind.com")
  ->onReportedRoute(function (Route $route, Report $report): void {
    echo "{$route->path()}: {$report->score(RuleType::Security)}" . PHP_EOL;
  })
  ->analyse();
```

This will display something like this:

```bash
https://example.com/: 38
https://northerwind.com/: 61
```

## Tests

```bash
composer install
composer run analyse
composer run test
composer run lint
composer run install-security-checker
composer run security-check
composer run update-check
```

## Compatibility table

This table shows the compatibility with PHP versions **for this current package version** only.

| PHP version | Supported |
|-------------|-----------|
| 8.1.*       | ✅        |
| <= 8.0.*    | ❌        |
