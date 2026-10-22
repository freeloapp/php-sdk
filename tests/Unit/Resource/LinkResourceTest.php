<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Resource;

use Freelo\Sdk\Http\FreeloClient;
use Freelo\Sdk\Http\Response;
use Freelo\Sdk\Http\ResponseParser;
use Freelo\Sdk\Resource\LinkResource;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class LinkResourceTest extends TestCase
{
    private const UUID = '11nk0000-0000-0000-0000-000000000000';
    private const ITEM = '{"uuid": "11nk0000-0000-0000-0000-000000000000", "name": "Brief", "type": "link",'
        . ' "link": "https://docs.google.com/document/d/1", "link_type": "google_doc", "note": "Draft"}';

    private FreeloClient $client;
    private LinkResource $resource;

    protected function setUp(): void
    {
        $this->client = $this->createMock(FreeloClient::class);
        $this->client->method('getResponseParser')
            ->willReturn(new ResponseParser());

        $this->resource = new LinkResource($this->client);
    }

    public function testCreate(): void
    {
        $this->client->expects($this->once())
            ->method('post')
            ->with('links', [
                'project_id' => 42,
                'name' => 'Brief',
                'link' => 'https://docs.google.com/document/d/1',
            ])
            ->willReturn($this->createSuccessResponse(self::ITEM, 201));

        $link = $this->resource->create(42, ['name' => 'Brief', 'link' => 'https://docs.google.com/document/d/1']);

        $this->assertTrue($link->isLink());
        $this->assertSame('google_doc', $link->linkType);
    }

    public function testUpdate(): void
    {
        $this->client->expects($this->once())
            ->method('patch')
            ->with('links/' . self::UUID, ['note' => null])
            ->willReturn($this->createSuccessResponse(self::ITEM));

        $this->assertSame(self::UUID, $this->resource->update(self::UUID, ['note' => null])->uuid);
    }

    public function testDelete(): void
    {
        $this->client->expects($this->once())
            ->method('delete')
            ->with('links/' . self::UUID)
            ->willReturn($this->createSuccessResponse('{"result": "success"}'));

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
