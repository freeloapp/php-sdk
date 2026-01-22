<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Exception;

use Freelo\Sdk\Exception\ApiException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ApiExceptionTest extends TestCase
{
    public function testFromResponseWithJsonError(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')
            ->willReturn('{"message": "Test error message"}');

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(400);
        $response->method('getBody')->willReturn($stream);

        $exception = ApiException::fromResponse($response);

        $this->assertInstanceOf(ApiException::class, $exception);
        $this->assertSame('Test error message', $exception->getMessage());
        $this->assertSame(400, $exception->getCode());
        $this->assertSame($response, $exception->getResponse());
    }

    public function testFromResponseWithoutJsonError(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')
            ->willReturn('Plain text error');

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(500);
        $response->method('getBody')->willReturn($stream);

        $exception = ApiException::fromResponse($response);

        $this->assertSame('API request failed with status code 500', $exception->getMessage());
        $this->assertSame(500, $exception->getCode());
    }

    public function testGetResponseData(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')
            ->willReturn('{"message": "Error", "code": "ERR_001"}');

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(400);
        $response->method('getBody')->willReturn($stream);

        $exception = ApiException::fromResponse($response);
        $responseData = $exception->getResponseData();

        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertArrayHasKey('code', $responseData);
        $this->assertSame('Error', $responseData['message']);
        $this->assertSame('ERR_001', $responseData['code']);
    }
}
