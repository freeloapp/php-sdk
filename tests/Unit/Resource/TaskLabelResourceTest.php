<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Resource;

use Freelo\Sdk\Http\FreeloClient;
use Freelo\Sdk\Http\Response;
use Freelo\Sdk\Http\ResponseParser;
use Freelo\Sdk\Resource\TaskLabelResource;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class TaskLabelResourceTest extends TestCase
{
    private FreeloClient $client;
    private ResponseParser $parser;
    private TaskLabelResource $resource;

    protected function setUp(): void
    {
        $this->client = $this->createMock(FreeloClient::class);
        $this->parser = new ResponseParser();

        $this->client->method('getResponseParser')
            ->willReturn($this->parser);

        $this->resource = new TaskLabelResource($this->client);
    }

    public function testCreate(): void
    {
        $labels = [
            ['name' => 'Bug', 'color' => '#ff0000'],
            ['name' => 'Feature', 'color' => '#00ff00'],
        ];

        $response = $this->createSuccessResponse('', 200);

        $this->client->expects($this->once())
            ->method('post')
            ->with('task-labels', ['labels' => $labels])
            ->willReturn($response);

        $result = $this->resource->create($labels);

        $this->assertTrue($result);
    }

    public function testAddToTask(): void
    {
        $labels = [
            ['name' => 'Bug', 'color' => '#ff0000'],
        ];

        $response = $this->createSuccessResponse('', 200);

        $this->client->expects($this->once())
            ->method('post')
            ->with('task-labels/add-to-task/123', ['labels' => $labels])
            ->willReturn($response);

        $result = $this->resource->addToTask(123, $labels);

        $this->assertTrue($result);
    }

    public function testRemoveFromTask(): void
    {
        $labels = [
            ['name' => 'Bug', 'color' => '#ff0000'],
        ];

        $response = $this->createSuccessResponse('', 200);

        $this->client->expects($this->once())
            ->method('post')
            ->with('task-labels/remove-from-task/456', ['labels' => $labels])
            ->willReturn($response);

        $result = $this->resource->removeFromTask(456, $labels);

        $this->assertTrue($result);
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
