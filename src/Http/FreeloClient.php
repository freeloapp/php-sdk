<?php

declare(strict_types=1);

namespace Freelo\Sdk\Http;

use Freelo\Sdk\Auth\Credentials;
use Freelo\Sdk\Exception\ApiException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Main HTTP client for Freelo API
 *
 * Handles all HTTP communication with the Freelo API using PSR-18 HTTP client.
 */
class FreeloClient
{
    private const DEFAULT_BASE_URL = 'https://api.freelo.io/v1';
    private const DEFAULT_USER_AGENT = 'Freelo-PHP-SDK/1.0';

    private string $baseUrl = self::DEFAULT_BASE_URL;
    private string $userAgent = self::DEFAULT_USER_AGENT;
    private readonly RateLimiter $rateLimiter;
    private readonly ?RetryHandler $retryHandler;

    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly Credentials $credentials,
        private readonly ResponseParser $responseParser = new ResponseParser(),
        ?RateLimiter $rateLimiter = null,
        ?RetryHandler $retryHandler = null,
    ) {
        $this->rateLimiter = $rateLimiter ?? new RateLimiter();
        $this->retryHandler = $retryHandler;
    }

    /**
     * Send a GET request
     *
     * @param string $uri
     * @param array<string, mixed> $queryParams
     * @return Response
     * @throws ApiException
     */
    public function get(string $uri, array $queryParams = []): Response
    {
        return $this->request('GET', $uri, [
            'query' => $queryParams,
        ]);
    }

    /**
     * Send a POST request
     *
     * @param string $uri
     * @param array<string, mixed> $data
     * @return Response
     * @throws ApiException
     */
    public function post(string $uri, array $data = []): Response
    {
        return $this->request('POST', $uri, [
            'json' => $data,
        ]);
    }

    /**
     * Send a PUT request
     *
     * @param string $uri
     * @param array<string, mixed> $data
     * @return Response
     * @throws ApiException
     */
    public function put(string $uri, array $data = []): Response
    {
        return $this->request('PUT', $uri, [
            'json' => $data,
        ]);
    }

    /**
     * Send a PATCH request
     *
     * @param string $uri
     * @param array<string, mixed> $data
     * @return Response
     * @throws ApiException
     */
    public function patch(string $uri, array $data = []): Response
    {
        return $this->request('PATCH', $uri, [
            'json' => $data,
        ]);
    }

    /**
     * Send a DELETE request
     *
     * @param string $uri
     * @return Response
     * @throws ApiException
     */
    public function delete(string $uri): Response
    {
        return $this->request('DELETE', $uri);
    }

    /**
     * Upload a file using multipart form data
     *
     * @param string $uri
     * @param string $filePath
     * @return Response
     * @throws ApiException
     */
    public function uploadFile(string $uri, string $filePath): Response
    {
        if (!file_exists($filePath)) {
            throw new ApiException("File not found: {$filePath}");
        }

        if (!is_readable($filePath)) {
            throw new ApiException("File is not readable: {$filePath}");
        }

        $boundary = 'FreeloPHPSDK' . uniqid();
        $filename = basename($filePath);
        $fileContent = file_get_contents($filePath);
        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

        $body = "--{$boundary}\r\n";
        $body .= "Content-Disposition: form-data; name=\"file\"; filename=\"{$filename}\"\r\n";
        $body .= "Content-Type: {$mimeType}\r\n\r\n";
        $body .= $fileContent . "\r\n";
        $body .= "--{$boundary}--\r\n";

        return $this->request('POST', $uri, [
            'headers' => [
                'Content-Type' => "multipart/form-data; boundary={$boundary}",
            ],
            'body' => $body,
        ]);
    }

    /**
     * Send an HTTP request
     *
     * @param string $method
     * @param string $uri
     * @param array<string, mixed> $options
     * @return Response
     * @throws ApiException
     */
    public function request(string $method, string $uri, array $options = []): Response
    {
        $executeRequest = function () use ($method, $uri, $options): Response {
            $request = $this->buildRequest($method, $uri, $options);

            try {
                $psrResponse = $this->httpClient->sendRequest($request);
                $response = Response::fromPsrResponse($psrResponse);

                // Update rate limiter from response headers
                $this->rateLimiter->updateFromHeaders($response->getHeaders());

                return $response;
            } catch (ClientExceptionInterface $e) {
                throw new ApiException(
                    'HTTP request failed: ' . $e->getMessage(),
                    0,
                    $e,
                );
            }
        };

        // Use retry handler if configured
        if ($this->retryHandler !== null) {
            return $this->retryHandler->execute($executeRequest);
        }

        return $executeRequest();
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array<string, mixed> $options
     * @return \Psr\Http\Message\RequestInterface
     */
    private function buildRequest(string $method, string $uri, array $options): \Psr\Http\Message\RequestInterface
    {
        $requestBuilder = new Request($this->requestFactory, $this->streamFactory);

        $fullUri = $this->buildFullUri($uri);
        $requestBuilder->setMethod($method);
        $requestBuilder->setUri($fullUri);

        // Set default headers
        $headers = [
            'User-Agent' => $this->userAgent,
            'Accept' => 'application/json',
        ];

        // Add authentication headers
        $headers = array_merge($headers, $this->credentials->getAuthHeaders());

        // Add custom headers from options
        if (isset($options['headers']) && is_array($options['headers'])) {
            $headers = array_merge($headers, $options['headers']);
        }

        $requestBuilder->setHeaders($headers);

        // Add query parameters
        if (isset($options['query']) && is_array($options['query'])) {
            $requestBuilder->setQueryParams($options['query']);
        }

        // Add JSON body
        if (isset($options['json']) && is_array($options['json'])) {
            $requestBuilder->setJsonBody($options['json']);
        }

        // Add raw body
        if (isset($options['body']) && is_string($options['body'])) {
            $requestBuilder->setBody($options['body']);
        }

        return $requestBuilder->build();
    }

    private function buildFullUri(string $uri): string
    {
        // If URI is already a full URL, return it
        if (str_starts_with($uri, 'http://') || str_starts_with($uri, 'https://')) {
            return $uri;
        }

        // Remove leading slash if present
        $uri = ltrim($uri, '/');

        return rtrim($this->baseUrl, '/') . '/' . $uri;
    }

    public function setBaseUrl(string $baseUrl): self
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        return $this;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function setUserAgent(string $userAgent): self
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function getResponseParser(): ResponseParser
    {
        return $this->responseParser;
    }

    public function getRateLimiter(): RateLimiter
    {
        return $this->rateLimiter;
    }

    public function getRetryHandler(): ?RetryHandler
    {
        return $this->retryHandler;
    }
}
