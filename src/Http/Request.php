<?php

declare(strict_types=1);

namespace Freelo\Sdk\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * HTTP Request builder
 *
 * Provides a fluent interface for building PSR-7 HTTP requests.
 */
class Request
{
    private string $method;
    private string $uri;

    /**
     * @var array<string, string>
     */
    private array $headers = [];

    /**
     * @var array<string, mixed>
     */
    private array $queryParams = [];

    /**
     * @var array<string, mixed>|null
     */
    private ?array $jsonBody = null;

    private ?string $body = null;

    public function __construct(
        private readonly RequestFactoryInterface $requestFactory,
        private readonly StreamFactoryInterface $streamFactory,
    ) {
    }

    public function setMethod(string $method): self
    {
        $this->method = strtoupper($method);
        return $this;
    }

    public function setUri(string $uri): self
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * @param array<string, string> $headers
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * @param array<string, mixed> $params
     */
    public function setQueryParams(array $params): self
    {
        $this->queryParams = array_merge($this->queryParams, $params);
        return $this;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function setJsonBody(array $data): self
    {
        $this->jsonBody = $data;
        $this->setHeader('Content-Type', 'application/json');
        return $this;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function build(): RequestInterface
    {
        $uri = $this->buildUri();
        $request = $this->requestFactory->createRequest($this->method, $uri);

        // Add headers
        foreach ($this->headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        // Add body
        if ($this->jsonBody !== null) {
            $body = json_encode($this->jsonBody, JSON_THROW_ON_ERROR);
            $stream = $this->streamFactory->createStream($body);
            $request = $request->withBody($stream);
        } elseif ($this->body !== null) {
            $stream = $this->streamFactory->createStream($this->body);
            $request = $request->withBody($stream);
        }

        return $request;
    }

    private function buildUri(): string
    {
        $uri = $this->uri;

        if (count($this->queryParams) > 0) {
            $query = http_build_query($this->queryParams);
            $separator = str_contains($uri, '?') ? '&' : '?';
            $uri .= $separator . $query;
        }

        return $uri;
    }

    public static function create(
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory,
    ): self {
        return new self($requestFactory, $streamFactory);
    }
}
