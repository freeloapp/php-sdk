<?php

declare(strict_types=1);

namespace Freelo\Sdk\Exception;

use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Exception thrown when API returns an error response
 */
class ApiException extends FreeloException
{
    private ?ResponseInterface $response = null;

    /**
     * @var array<string, mixed>
     */
    private array $responseData = [];

    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        ?ResponseInterface $response = null,
    ) {
        parent::__construct($message, $code, $previous);
        $this->response = $response;

        if ($response !== null) {
            $this->parseResponseData($response);
        }
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    /**
     * Get the HTTP status code
     */
    public function getStatusCode(): ?int
    {
        return $this->response?->getStatusCode();
    }

    /**
     * @return array<string, mixed>
     */
    public function getResponseData(): array
    {
        return $this->responseData;
    }

    private function parseResponseData(ResponseInterface $response): void
    {
        try {
            $body = (string) $response->getBody();
            $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
            $this->responseData = is_array($data) ? $data : [];
        } catch (\JsonException) {
            $this->responseData = [];
        }
    }

    public static function fromResponse(ResponseInterface $response, ?Throwable $previous = null): self
    {
        $statusCode = $response->getStatusCode();
        $body = (string) $response->getBody();

        try {
            $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
            $message = is_array($data) && isset($data['message'])
                ? (string) $data['message']
                : "API request failed with status code {$statusCode}";
        } catch (\JsonException) {
            $message = "API request failed with status code {$statusCode}";
        }

        return new self($message, $statusCode, $previous, $response);
    }
}
