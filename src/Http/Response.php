<?php

declare(strict_types=1);

namespace Freelo\Sdk\Http;

use Freelo\Sdk\Exception\ApiException;
use Freelo\Sdk\Exception\AuthenticationException;
use Freelo\Sdk\Exception\NotFoundException;
use Freelo\Sdk\Exception\RateLimitException;
use Freelo\Sdk\Exception\ValidationException;
use Psr\Http\Message\ResponseInterface;

/**
 * HTTP Response handler
 *
 * Handles PSR-7 responses and provides methods for parsing and error handling.
 */
class Response
{
    public function __construct(
        private readonly ResponseInterface $response,
    ) {
    }

    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    public function isSuccessful(): bool
    {
        $statusCode = $this->getStatusCode();
        return $statusCode >= 200 && $statusCode < 300;
    }

    public function getBody(): string
    {
        return (string) $this->response->getBody();
    }

    /**
     * @return array<string, mixed>
     * @throws \JsonException
     */
    public function json(): array
    {
        $body = $this->getBody();

        if (empty($body)) {
            return [];
        }

        $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        return is_array($data) ? $data : [];
    }

    /**
     * @return array<string, string[]>
     */
    public function getHeaders(): array
    {
        return $this->response->getHeaders();
    }

    /**
     * @return string[]
     */
    public function getHeader(string $name): array
    {
        return $this->response->getHeader($name);
    }

    public function getHeaderLine(string $name): string
    {
        return $this->response->getHeaderLine($name);
    }

    public function getPsrResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * Throw an appropriate exception if the response indicates an error
     *
     * @throws ApiException
     */
    public function throwIfError(): void
    {
        if ($this->isSuccessful()) {
            return;
        }

        $statusCode = $this->getStatusCode();

        // Handle specific status codes
        match (true) {
            $statusCode === 401 => throw new AuthenticationException(
                'Authentication failed',
                $statusCode,
                null,
                $this->response,
            ),
            $statusCode === 404 => throw new NotFoundException(
                'Resource not found',
                $statusCode,
                null,
                $this->response,
            ),
            $statusCode === 422 => throw $this->createValidationException(),
            $statusCode === 429 => throw $this->createRateLimitException(),
            default => throw ApiException::fromResponse($this->response),
        };
    }

    private function createValidationException(): ValidationException
    {
        $exception = new ValidationException(
            'Validation failed',
            422,
            null,
            $this->response,
        );

        try {
            $data = $this->json();
            if (isset($data['errors']) && is_array($data['errors'])) {
                $exception->setErrors($data['errors']);
            }
        } catch (\JsonException) {
            // Ignore JSON parsing errors for validation exceptions
        }

        return $exception;
    }

    private function createRateLimitException(): RateLimitException
    {
        $exception = new RateLimitException(
            'Rate limit exceeded',
            429,
            null,
            $this->response,
        );

        $retryAfter = $this->getHeaderLine('Retry-After');
        if ($retryAfter !== '' && is_numeric($retryAfter)) {
            $exception->setRetryAfter((int) $retryAfter);
        }

        return $exception;
    }

    public static function fromPsrResponse(ResponseInterface $response): self
    {
        return new self($response);
    }
}
