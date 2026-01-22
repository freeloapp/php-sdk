<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Http;

use Freelo\Sdk\Exception\ApiException;
use Freelo\Sdk\Exception\AuthenticationException;
use Freelo\Sdk\Exception\NotFoundException;
use Freelo\Sdk\Exception\RateLimitException;
use Freelo\Sdk\Exception\ValidationException;
use Freelo\Sdk\Http\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ResponseTest extends TestCase
{
    public function testGetStatusCode(): void
    {
        $psrResponse = $this->createMock(ResponseInterface::class);
        $psrResponse->method('getStatusCode')->willReturn(200);

        $response = new Response($psrResponse);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testIsSuccessful(): void
    {
        $psrResponse = $this->createMock(ResponseInterface::class);
        $psrResponse->method('getStatusCode')->willReturn(200);

        $response = new Response($psrResponse);

        $this->assertTrue($response->isSuccessful());
    }

    public function testIsNotSuccessful(): void
    {
        $psrResponse = $this->createMock(ResponseInterface::class);
        $psrResponse->method('getStatusCode')->willReturn(404);

        $response = new Response($psrResponse);

        $this->assertFalse($response->isSuccessful());
    }

    public function testGetBody(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn('response body');

        $psrResponse = $this->createMock(ResponseInterface::class);
        $psrResponse->method('getBody')->willReturn($stream);

        $response = new Response($psrResponse);

        $this->assertSame('response body', $response->getBody());
    }

    public function testJson(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn('{"name":"Test","value":123}');

        $psrResponse = $this->createMock(ResponseInterface::class);
        $psrResponse->method('getBody')->willReturn($stream);

        $response = new Response($psrResponse);
        $data = $response->json();

        $this->assertIsArray($data);
        $this->assertSame('Test', $data['name']);
        $this->assertSame(123, $data['value']);
    }

    public function testJsonWithEmptyBody(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn('');

        $psrResponse = $this->createMock(ResponseInterface::class);
        $psrResponse->method('getBody')->willReturn($stream);

        $response = new Response($psrResponse);
        $data = $response->json();

        $this->assertIsArray($data);
        $this->assertEmpty($data);
    }

    public function testJsonThrowsExceptionOnInvalidJson(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn('invalid json');

        $psrResponse = $this->createMock(ResponseInterface::class);
        $psrResponse->method('getBody')->willReturn($stream);

        $response = new Response($psrResponse);

        $this->expectException(\JsonException::class);
        $response->json();
    }

    public function testGetHeaders(): void
    {
        $psrResponse = $this->createMock(ResponseInterface::class);
        $psrResponse->method('getHeaders')->willReturn(['Content-Type' => ['application/json']]);

        $response = new Response($psrResponse);
        $headers = $response->getHeaders();

        $this->assertIsArray($headers);
        $this->assertArrayHasKey('Content-Type', $headers);
    }

    public function testGetHeader(): void
    {
        $psrResponse = $this->createMock(ResponseInterface::class);
        $psrResponse->method('getHeader')->with('Content-Type')->willReturn(['application/json']);

        $response = new Response($psrResponse);
        $header = $response->getHeader('Content-Type');

        $this->assertIsArray($header);
        $this->assertSame(['application/json'], $header);
    }

    public function testGetHeaderLine(): void
    {
        $psrResponse = $this->createMock(ResponseInterface::class);
        $psrResponse->method('getHeaderLine')->with('Content-Type')->willReturn('application/json');

        $response = new Response($psrResponse);
        $headerLine = $response->getHeaderLine('Content-Type');

        $this->assertSame('application/json', $headerLine);
    }

    public function testGetPsrResponse(): void
    {
        $psrResponse = $this->createMock(ResponseInterface::class);
        $response = new Response($psrResponse);

        $this->assertSame($psrResponse, $response->getPsrResponse());
    }

    public function testThrowIfErrorDoesNotThrowOnSuccess(): void
    {
        $psrResponse = $this->createMock(ResponseInterface::class);
        $psrResponse->method('getStatusCode')->willReturn(200);

        $response = new Response($psrResponse);

        // Should not throw
        $response->throwIfError();
        $this->assertTrue(true);
    }

    public function testThrowIfErrorThrowsAuthenticationException(): void
    {
        $psrResponse = $this->createMock(ResponseInterface::class);
        $psrResponse->method('getStatusCode')->willReturn(401);

        $response = new Response($psrResponse);

        $this->expectException(AuthenticationException::class);
        $response->throwIfError();
    }

    public function testThrowIfErrorThrowsNotFoundException(): void
    {
        $psrResponse = $this->createMock(ResponseInterface::class);
        $psrResponse->method('getStatusCode')->willReturn(404);

        $response = new Response($psrResponse);

        $this->expectException(NotFoundException::class);
        $response->throwIfError();
    }

    public function testThrowIfErrorThrowsValidationException(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn('{"errors":{"field":"error message"}}');

        $psrResponse = $this->createMock(ResponseInterface::class);
        $psrResponse->method('getStatusCode')->willReturn(422);
        $psrResponse->method('getBody')->willReturn($stream);

        $response = new Response($psrResponse);

        $this->expectException(ValidationException::class);
        $response->throwIfError();
    }

    public function testThrowIfErrorThrowsRateLimitException(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn('{}');

        $psrResponse = $this->createMock(ResponseInterface::class);
        $psrResponse->method('getStatusCode')->willReturn(429);
        $psrResponse->method('getBody')->willReturn($stream);
        $psrResponse->method('getHeaderLine')->with('Retry-After')->willReturn('60');

        $response = new Response($psrResponse);

        $this->expectException(RateLimitException::class);
        $response->throwIfError();
    }

    public function testThrowIfErrorThrowsApiException(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn('{"message":"Server error"}');

        $psrResponse = $this->createMock(ResponseInterface::class);
        $psrResponse->method('getStatusCode')->willReturn(500);
        $psrResponse->method('getBody')->willReturn($stream);

        $response = new Response($psrResponse);

        $this->expectException(ApiException::class);
        $response->throwIfError();
    }

    public function testFromPsrResponse(): void
    {
        $psrResponse = $this->createMock(ResponseInterface::class);
        $response = Response::fromPsrResponse($psrResponse);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame($psrResponse, $response->getPsrResponse());
    }
}
