<?php

namespace Khalyomede;

use GuzzleHttp\Psr7\Response as Psr7Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class Response implements ResponseInterface
{
    public function __construct(private ResponseInterface $originalResponse = new Psr7Response(), private float $responseTimeInSeconds = 0.0)
    {
    }

    public function setOriginalResponse(ResponseInterface $response): self
    {
        $this->originalResponse = $response;

        return $this;
    }

    public function setResponseTimeInSeconds(float $time): self
    {
        $this->responseTimeInSeconds = $time;

        return $this;
    }

    public function getResponseTimeInSeconds(): float
    {
        return $this->responseTimeInSeconds;
    }

    public function getStatusCode(): int
    {
        return $this->originalResponse->getStatusCode();
    }

    public function withStatus($code, $reasonPhrase = ''): static
    {
        return $this->originalResponse->withStatus($code, $reasonPhrase);
    }

    public function getReasonPhrase(): string
    {
        return $this->originalResponse->getReasonPhrase();
    }

    public function getProtocolVersion(): string
    {
        return $this->originalResponse->getProtocolVersion();
    }

    public function withProtocolVersion($version): static
    {
        return $this->originalResponse->withProtocolVersion($version);
    }

    public function getHeaders(): array
    {
        return $this->originalResponse->getHeaders();
    }

    public function hasHeader($name): bool
    {
        return $this->originalResponse->hasHeader($name);
    }

    public function getHeader($name): array
    {
        return $this->originalResponse->getHeader($name);
    }

    public function getHeaderLine($name): string
    {
        return $this->originalResponse->getHeaderLine($name);
    }

    public function withHeader($name, $value): static
    {
        return $this->originalResponse->withHeader($name, $value);
    }

    public function withAddedHeader($name, $value): static
    {
        return $this->originalResponse->withAddedHeader($name, $value);
    }

    public function withoutHeader($name): static
    {
        return $this->originalResponse->withoutHeader($name);
    }

    public function getBody(): StreamInterface
    {
        return $this->originalResponse->getBody();
    }

    public function withBody(StreamInterface $body): static
    {
        return $this->originalResponse->withBody($body);
    }
}
