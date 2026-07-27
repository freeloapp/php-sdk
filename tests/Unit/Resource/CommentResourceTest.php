<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Resource;

use Freelo\Sdk\Http\FreeloClient;
use Freelo\Sdk\Http\Response;
use Freelo\Sdk\Http\ResponseParser;
use Freelo\Sdk\Resource\CommentResource;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class CommentResourceTest extends TestCase
{
    private FreeloClient $client;
    private CommentResource $resource;

    protected function setUp(): void
    {
        $this->client = $this->createMock(FreeloClient::class);
        $parser = new ResponseParser();

        $this->client->method('getResponseParser')
            ->willReturn($parser);

        $this->resource = new CommentResource($this->client);
    }

    public function testUpdate(): void
    {
        $responseData = ['id' => 42, 'content' => 'Fixed typo'];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('post')
            ->with('comment/42', ['content' => 'Fixed typo'])
            ->willReturn($response);

        $comment = $this->resource->update(42, 'Fixed typo');

        $this->assertSame(42, $comment->id);
        $this->assertSame('Fixed typo', $comment->content);
    }

    public function testUpdateWithFiles(): void
    {
        $responseData = ['id' => 42, 'content' => 'With attachment'];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('post')
            ->with('comment/42', [
                'content' => 'With attachment',
                'files' => ['a1b2c3d4-0000-0000-0000-000000000000'],
            ])
            ->willReturn($response);

        $this->resource->update(42, 'With attachment', ['a1b2c3d4-0000-0000-0000-000000000000']);
    }

    public function testDelete(): void
    {
        $response = $this->createSuccessResponse('{"result": "success"}');

        $this->client->expects($this->once())
            ->method('delete')
            ->with('comment/42')
            ->willReturn($response);

        $this->assertTrue($this->resource->delete(42));
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
