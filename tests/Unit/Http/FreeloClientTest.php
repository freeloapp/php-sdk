<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Http;

use Freelo\Sdk\Auth\Credentials;
use Freelo\Sdk\Exception\ApiException;
use Freelo\Sdk\Http\FreeloClient;
use Freelo\Sdk\Http\Response;
use Freelo\Sdk\Http\ResponseParser;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

class FreeloClientTest extends TestCase
{
    private ClientInterface $httpClient;
    private RequestFactoryInterface $requestFactory;
    private StreamFactoryInterface $streamFactory;
    private Credentials $credentials;
    private FreeloClient $client;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(ClientInterface::class);
        $this->requestFactory = $this->createMock(RequestFactoryInterface::class);
        $this->streamFactory = $this->createMock(StreamFactoryInterface::class);
        $this->credentials = $this->createMock(Credentials::class);

        $this->client = new FreeloClient(
            $this->httpClient,
            $this->requestFactory,
            $this->streamFactory,
            $this->credentials,
        );
    }

    public function testGetRequest(): void
    {
        $this->setupSuccessfulRequest('GET', 'projects', '{"data":[]}');

        $response = $this->client->get('projects');

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testGetRequestWithQueryParams(): void
    {
        $this->setupSuccessfulRequest('GET', 'projects?page=1&limit=10', '{"data":[]}');

        $response = $this->client->get('projects', ['page' => 1, 'limit' => 10]);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testPostRequest(): void
    {
        $this->setupSuccessfulRequest('POST', 'projects', '{"id":1}');

        $response = $this->client->post('projects', ['name' => 'Test Project']);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testPutRequest(): void
    {
        $this->setupSuccessfulRequest('PUT', 'projects/1', '{"id":1}');

        $response = $this->client->put('projects/1', ['name' => 'Updated']);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testPatchRequest(): void
    {
        $this->setupSuccessfulRequest('PATCH', 'projects/1', '{"id":1}');

        $response = $this->client->patch('projects/1', ['name' => 'Patched']);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testDeleteRequest(): void
    {
        $this->setupSuccessfulRequest('DELETE', 'projects/1', '');

        $response = $this->client->delete('projects/1');

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testSetAndGetBaseUrl(): void
    {
        $customUrl = 'https://custom-api.example.com/v2';
        $result = $this->client->setBaseUrl($customUrl);

        $this->assertSame($this->client, $result, 'Should return self for chaining');
        $this->assertSame($customUrl, $this->client->getBaseUrl());
    }

    public function testSetAndGetUserAgent(): void
    {
        $customAgent = 'My-Custom-Agent/2.0';
        $result = $this->client->setUserAgent($customAgent);

        $this->assertSame($this->client, $result, 'Should return self for chaining');
        $this->assertSame($customAgent, $this->client->getUserAgent());
    }

    public function testGetResponseParser(): void
    {
        $parser = $this->client->getResponseParser();

        $this->assertInstanceOf(ResponseParser::class, $parser);
    }

    public function testRequestThrowsApiExceptionOnClientError(): void
    {
        $exception = new class ('Connection failed') extends \Exception implements ClientExceptionInterface {
        };

        $psrRequest = $this->createMock(RequestInterface::class);
        $psrRequest->method('withHeader')->willReturnSelf();

        $this->requestFactory->expects($this->once())
            ->method('createRequest')
            ->willReturn($psrRequest);

        $this->credentials->expects($this->once())
            ->method('getAuthHeaders')
            ->willReturn([]);

        $this->httpClient->expects($this->once())
            ->method('sendRequest')
            ->willThrowException($exception);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('HTTP request failed: Connection failed');

        $this->client->get('projects');
    }

    public function testBuildsFullUriWithRelativePath(): void
    {
        $this->setupSuccessfulRequest('GET', 'https://api.freelo.io/v1/projects', '{}');

        $this->client->get('projects');
    }

    public function testBuildsFullUriWithLeadingSlash(): void
    {
        $this->setupSuccessfulRequest('GET', 'https://api.freelo.io/v1/projects', '{}');

        $this->client->get('/projects');
    }

    public function testBuildsFullUriWithAbsoluteUrl(): void
    {
        $this->setupSuccessfulRequest('GET', 'https://custom.example.com/api', '{}');

        $this->client->get('https://custom.example.com/api');
    }

    private function setupSuccessfulRequest(string $method, string $expectedUri, string $responseBody): void
    {
        $psrRequest = $this->createMock(RequestInterface::class);
        $psrRequest->method('withHeader')->willReturnSelf();
        $psrRequest->method('withBody')->willReturnSelf();

        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn($responseBody);

        $psrResponse = $this->createMock(ResponseInterface::class);
        $psrResponse->method('getStatusCode')->willReturn(200);
        $psrResponse->method('getBody')->willReturn($stream);

        $this->requestFactory->expects($this->once())
            ->method('createRequest')
            ->with($method, $this->callback(function ($uri) use ($expectedUri) {
                // Allow flexible URI matching
                return str_contains((string) $uri, $expectedUri) || $uri === $expectedUri;
            }))
            ->willReturn($psrRequest);

        $this->streamFactory->method('createStream')->willReturn($stream);

        $this->credentials->expects($this->once())
            ->method('getAuthHeaders')
            ->willReturn(['X-Api-Key' => 'test-key']);

        $this->httpClient->expects($this->once())
            ->method('sendRequest')
            ->with($psrRequest)
            ->willReturn($psrResponse);
    }
}
