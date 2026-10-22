<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Resource;

use Freelo\Sdk\Http\FreeloClient;
use Freelo\Sdk\Http\Response;
use Freelo\Sdk\Http\ResponseParser;
use Freelo\Sdk\Resource\ProjectFileResource;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ProjectFileResourceTest extends TestCase
{
    private const UUID = 'f11e0000-0000-0000-0000-000000000000';
    private const ITEM = '{"uuid": "f11e0000-0000-0000-0000-000000000000", "name": "Contract", "type": "file",'
        . ' "filename": "a1b2.pdf", "original_filename": "contract.pdf", "caption": "Contract", "size": 1024}';

    private FreeloClient $client;
    private ProjectFileResource $resource;

    protected function setUp(): void
    {
        $this->client = $this->createMock(FreeloClient::class);
        $this->client->method('getResponseParser')
            ->willReturn(new ResponseParser());

        $this->resource = new ProjectFileResource($this->client);
    }

    public function testUpload(): void
    {
        $this->client->expects($this->once())
            ->method('uploadFile')
            ->with('project-files', '/tmp/contract.pdf', ['project_id' => 42, 'caption' => 'Contract'])
            ->willReturn($this->createSuccessResponse(self::ITEM, 201));

        $file = $this->resource->upload(42, '/tmp/contract.pdf', ['caption' => 'Contract']);

        $this->assertTrue($file->isFile());
        $this->assertSame('Contract', $file->getDisplayName());
        $this->assertSame(1024, $file->size);
    }

    public function testUpdate(): void
    {
        $this->client->expects($this->once())
            ->method('patch')
            ->with('project-files/' . self::UUID, ['caption' => 'Signed contract'])
            ->willReturn($this->createSuccessResponse(self::ITEM));

        $this->assertSame(self::UUID, $this->resource->update(self::UUID, ['caption' => 'Signed contract'])->uuid);
    }

    public function testDelete(): void
    {
        $this->client->expects($this->once())
            ->method('delete')
            ->with('project-files/' . self::UUID)
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
