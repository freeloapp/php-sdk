<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Http;

use Freelo\Sdk\Http\Request;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

class RequestTest extends TestCase
{
    private RequestFactoryInterface $requestFactory;
    private StreamFactoryInterface $streamFactory;
    private Request $request;

    protected function setUp(): void
    {
        $this->requestFactory = $this->createMock(RequestFactoryInterface::class);
        $this->streamFactory = $this->createMock(StreamFactoryInterface::class);
        $this->request = new Request($this->requestFactory, $this->streamFactory);
    }

    public function testSetMethod(): void
    {
        $result = $this->request->setMethod('get');

        $this->assertSame($this->request, $result, 'Should return self for chaining');
    }

    public function testSetMethodConvertsToUpperCase(): void
    {
        $psrRequest = $this->createMock(RequestInterface::class);

        $this->requestFactory->expects($this->once())
            ->method('createRequest')
            ->with('POST', 'https://api.example.com')
            ->willReturn($psrRequest);

        $this->request
            ->setMethod('post')
            ->setUri('https://api.example.com')
            ->build();
    }

    public function testSetUri(): void
    {
        $result = $this->request->setUri('https://api.example.com');

        $this->assertSame($this->request, $result, 'Should return self for chaining');
    }

    public function testSetHeader(): void
    {
        $psrRequest = $this->createMock(RequestInterface::class);
        $psrRequest->expects($this->once())
            ->method('withHeader')
            ->with('X-Custom-Header', 'value')
            ->willReturnSelf();

        $this->requestFactory->expects($this->once())
            ->method('createRequest')
            ->willReturn($psrRequest);

        $this->request
            ->setMethod('GET')
            ->setUri('https://api.example.com')
            ->setHeader('X-Custom-Header', 'value')
            ->build();
    }

    public function testSetHeaders(): void
    {
        $psrRequest = $this->createMock(RequestInterface::class);
        $psrRequest->expects($this->exactly(2))
            ->method('withHeader')
            ->willReturnSelf();

        $this->requestFactory->expects($this->once())
            ->method('createRequest')
            ->willReturn($psrRequest);

        $this->request
            ->setMethod('GET')
            ->setUri('https://api.example.com')
            ->setHeaders(['X-Header-1' => 'value1', 'X-Header-2' => 'value2'])
            ->build();
    }

    public function testSetQueryParams(): void
    {
        $psrRequest = $this->createMock(RequestInterface::class);

        $this->requestFactory->expects($this->once())
            ->method('createRequest')
            ->with('GET', 'https://api.example.com?foo=bar&baz=qux')
            ->willReturn($psrRequest);

        $this->request
            ->setMethod('GET')
            ->setUri('https://api.example.com')
            ->setQueryParams(['foo' => 'bar', 'baz' => 'qux'])
            ->build();
    }

    public function testSetQueryParamsAppendsToExistingQuery(): void
    {
        $psrRequest = $this->createMock(RequestInterface::class);

        $this->requestFactory->expects($this->once())
            ->method('createRequest')
            ->with('GET', 'https://api.example.com?existing=param&foo=bar')
            ->willReturn($psrRequest);

        $this->request
            ->setMethod('GET')
            ->setUri('https://api.example.com?existing=param')
            ->setQueryParams(['foo' => 'bar'])
            ->build();
    }

    public function testSetJsonBody(): void
    {
        $psrRequest = $this->createMock(RequestInterface::class);
        $psrRequest->expects($this->once())
            ->method('withHeader')
            ->with('Content-Type', 'application/json')
            ->willReturnSelf();

        $stream = $this->createMock(StreamInterface::class);

        $psrRequest->expects($this->once())
            ->method('withBody')
            ->with($stream)
            ->willReturnSelf();

        $this->streamFactory->expects($this->once())
            ->method('createStream')
            ->with('{"name":"Test","value":123}')
            ->willReturn($stream);

        $this->requestFactory->expects($this->once())
            ->method('createRequest')
            ->willReturn($psrRequest);

        $this->request
            ->setMethod('POST')
            ->setUri('https://api.example.com')
            ->setJsonBody(['name' => 'Test', 'value' => 123])
            ->build();
    }

    public function testSetBody(): void
    {
        $psrRequest = $this->createMock(RequestInterface::class);
        $stream = $this->createMock(StreamInterface::class);

        $psrRequest->expects($this->once())
            ->method('withBody')
            ->with($stream)
            ->willReturnSelf();

        $this->streamFactory->expects($this->once())
            ->method('createStream')
            ->with('raw body content')
            ->willReturn($stream);

        $this->requestFactory->expects($this->once())
            ->method('createRequest')
            ->willReturn($psrRequest);

        $this->request
            ->setMethod('POST')
            ->setUri('https://api.example.com')
            ->setBody('raw body content')
            ->build();
    }

    public function testBuildReturnsPsrRequest(): void
    {
        $psrRequest = $this->createMock(RequestInterface::class);

        $this->requestFactory->expects($this->once())
            ->method('createRequest')
            ->willReturn($psrRequest);

        $result = $this->request
            ->setMethod('GET')
            ->setUri('https://api.example.com')
            ->build();

        $this->assertSame($psrRequest, $result);
    }

    public function testCreateStaticFactory(): void
    {
        $request = Request::create($this->requestFactory, $this->streamFactory);

        $this->assertInstanceOf(Request::class, $request);
    }
}
