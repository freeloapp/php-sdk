<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Resource;

use Freelo\Sdk\Http\FreeloClient;
use Freelo\Sdk\Http\Response;
use Freelo\Sdk\Http\ResponseParser;
use Freelo\Sdk\Resource\FileResource;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class FileResourceTest extends TestCase
{
    private const UUID = 'a1b2c3d4-0000-0000-0000-000000000000';

    private FreeloClient $client;
    private FileResource $resource;

    protected function setUp(): void
    {
        $this->client = $this->createMock(FreeloClient::class);
        $parser = new ResponseParser();

        $this->client->method('getResponseParser')
            ->willReturn($parser);

        $this->resource = new FileResource($this->client);
    }

    public function testDownload(): void
    {
        $response = $this->createSuccessResponse('raw-file-bytes');

        $this->client->expects($this->once())
            ->method('get')
            ->with('file/' . self::UUID)
            ->willReturn($response);

        $this->assertSame('raw-file-bytes', $this->resource->download(self::UUID));
    }

    public function testDelete(): void
    {
        $response = $this->createSuccessResponse('{"result": "success"}');

        $this->client->expects($this->once())
            ->method('delete')
            ->with('file/' . self::UUID)
            ->willReturn($response);

        $this->assertTrue($this->resource->delete(self::UUID));
    }

    private function createSuccessResponse(string $body, int $statusCode = 200): Response
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn($body);

        $psrResponse = $this->createMock(ResponseInterface::class);
        $psrResponse->method('getStatusCode')->willReturn($statusCode);
        $psrResponse->method('getBody')->willReturn($stream);

        return new Response($psrResponse);
    }
}
