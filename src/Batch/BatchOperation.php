<?php

declare(strict_types=1);

namespace Freelo\Sdk\Batch;

/**
 * Represents a single operation in a batch request
 */
class BatchOperation
{
    /**
     * @param string $method HTTP method
     * @param string $uri Request URI
     * @param array<string, mixed> $options Request options
     * @param string|null $key Optional key to identify this operation
     */
    public function __construct(
        private readonly string $method,
        private readonly string $uri,
        private readonly array $options = [],
        private readonly ?string $key = null,
    ) {
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }
}
