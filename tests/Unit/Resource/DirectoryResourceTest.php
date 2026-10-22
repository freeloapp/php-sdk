<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Resource;

use Freelo\Sdk\Http\FreeloClient;
use Freelo\Sdk\Http\Response;
use Freelo\Sdk\Http\ResponseParser;
use Freelo\Sdk\Resource\DirectoryResource;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class DirectoryResourceTest extends TestCase
{
    private const UUID = 'd1r00000-0000-0000-0000-000000000000';
    private const ITEM = '{"uuid": "d1r00000-0000-0000-0000-000000000000", "name": "Contracts",'
        . ' "type": "directory", "color": "#367fee", "items_count": 3, "directory_uuid": null}';

    private FreeloClient $client;
    private DirectoryResource $resource;

    protected function setUp(): void
    {
        $this->client = $this->createMock(FreeloClient::class);
        $this->client->method('getResponseParser')
            ->willReturn(new ResponseParser());

        $this->resource = new DirectoryResource($this->client);
    }

    public function testCreate(): void
    {
        $this->client->expects($this->once())
            ->method('post')
            ->with('directories', ['project_id' => 42, 'name' => 'Contracts', 'color' => '#367fee'])
            ->willReturn($this->createSuccessResponse(self::ITEM, 201));

        $directory = $this->resource->create(42, ['name' => 'Contracts', 'color' => '#367fee']);

        $this->assertSame(self::UUID, $directory->uuid);
        $this->assertTrue($directory->isDirectory());
        $this->assertSame(3, $directory->itemsCount);
    }

    public function testGet(): void
    {
        $this->client->expects($this->once())
            ->method('get')
            ->with('directories/' . self::UUID)
            ->willReturn($this->createSuccessResponse(self::ITEM));

        $this->assertSame('Contracts', $this->resource->get(self::UUID)->filename);
    }

    public function testUpdateSendsOnlyGivenFields(): void
    {
        $this->client->expects($this->once())
            ->method('patch')
            ->with('directories/' . self::UUID, ['directory_uuid' => null])
            ->willReturn($this->createSuccessResponse(self::ITEM));

        $this->assertNull($this->resource->update(self::UUID, ['directory_uuid' => null])->directoryUuid);
    }

    public function testDelete(): void
    {
        $this->client->expects($this->once())
            ->method('delete')
            ->with('directories/' . self::UUID, [])
            ->willReturn($this->createSuccessResponse('{"result": "success"}'));

        $this->assertTrue($this->resource->delete(self::UUID));
    }

    public function testForceDelete(): void
    {
        $this->client->expects($this->once())
            ->method('delete')
            ->with('directories/' . self::UUID, ['force' => 'true'])
            ->willReturn($this->createSuccessResponse('{"result": "success"}'));

        $this->assertTrue($this->resource->delete(self::UUID, force: true));
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
