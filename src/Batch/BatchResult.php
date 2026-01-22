<?php

declare(strict_types=1);

namespace Freelo\Sdk\Batch;

use Freelo\Sdk\Http\Response;
use Freelo\Sdk\Exception\ApiException;

/**
 * Result of a single batch operation
 */
class BatchResult
{
    private function __construct(
        private readonly BatchOperation $operation,
        private readonly bool $success,
        private readonly ?Response $response = null,
        private readonly ?ApiException $exception = null,
    ) {
    }

    /**
     * Create a successful result
     */
    public static function success(BatchOperation $operation, Response $response): self
    {
        return new self($operation, true, $response, null);
    }

    /**
     * Create a failed result
     */
    public static function failure(BatchOperation $operation, ApiException $exception): self
    {
        return new self($operation, false, null, $exception);
    }

    /**
     * Check if the operation was successful
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * Check if the operation failed
     */
    public function isFailure(): bool
    {
        return !$this->success;
    }

    /**
     * Get the operation
     */
    public function getOperation(): BatchOperation
    {
        return $this->operation;
    }

    /**
     * Get the response (only available if successful)
     */
    public function getResponse(): ?Response
    {
        return $this->response;
    }

    /**
     * Get the exception (only available if failed)
     */
    public function getException(): ?ApiException
    {
        return $this->exception;
    }

    /**
     * Get the operation key
     */
    public function getKey(): ?string
    {
        return $this->operation->getKey();
    }
}
