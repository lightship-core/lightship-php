# Lightship

Web page performance and SEO analysis, browser-less.

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

## About

I made a web app, and I want to be able to frequently test my public facing pages respond to various criterias to optimize their referencing.

Since my web app is fully server-side, all my tests are very fast, and do not rely on a web browser. I wanted to stick to this, and have a fast tool to get simple insights, like not ommiting the alt attributes on images, to ensure my description and title are filled, or to evaluate aproximatively my page load time.
