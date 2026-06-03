<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Resource;

use Freelo\Sdk\Http\FreeloClient;
use Freelo\Sdk\Http\PaginatedResult;
use Freelo\Sdk\Http\Response;
use Freelo\Sdk\Http\ResponseParser;
use Freelo\Sdk\Model\Subtask;
use Freelo\Sdk\Resource\SubtaskResource;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class SubtaskResourceTest extends TestCase
{
    private FreeloClient $client;
    private SubtaskResource $resource;

    protected function setUp(): void
    {
        $this->client = $this->createMock(FreeloClient::class);
        $parser = new ResponseParser();

        $this->client->method('getResponseParser')
            ->willReturn($parser);

        $this->resource = new SubtaskResource($this->client);
    }

    public function testList(): void
    {
        $responseData = [
            'data' => [
                ['id' => 1, 'name' => 'Subtask 1', 'type' => 'subtask', 'task_id' => 10],
                ['id' => 2, 'name' => 'Checklist item', 'type' => 'taskcheck', 'task_id' => null],
            ],
            'current_page' => 1,
            'total_pages' => 1,
            'per_page' => 10,
            'total' => 2,
        ];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('get')
            ->with('task/123/subtasks', [])
            ->willReturn($response);

        $result = $this->resource->list(123);

        $this->assertInstanceOf(PaginatedResult::class, $result);
        $this->assertCount(2, $result);
    }

    public function testCreate(): void
    {
        $createData = ['name' => 'New Subtask'];
        $responseData = ['id' => 999, 'name' => 'New Subtask'];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('post')
            ->with('task/123/subtasks', $createData)
            ->willReturn($response);

        $subtask = $this->resource->create(123, $createData);

        $this->assertInstanceOf(Subtask::class, $subtask);
        $this->assertSame(999, $subtask->id);
    }

    public function testUpdateTaskcheck(): void
    {
        $editData = ['name' => 'Renamed item', 'worker' => 42];
        $response = $this->createSuccessResponse('{"result": "success"}');

        $this->client->expects($this->once())
            ->method('post')
            ->with('taskcheck/456', $editData)
            ->willReturn($response);

        $this->assertTrue($this->resource->updateTaskcheck(456, $editData));
    }

    public function testDeleteTaskcheck(): void
    {
        $response = $this->createSuccessResponse('{"result": "success"}');

        $this->client->expects($this->once())
            ->method('delete')
            ->with('taskcheck/456')
            ->willReturn($response);

        $this->assertTrue($this->resource->deleteTaskcheck(456));
    }

    public function testFinishTaskcheck(): void
    {
        $response = $this->createSuccessResponse('{"result": "success"}');

        $this->client->expects($this->once())
            ->method('post')
            ->with('taskcheck/456/finish')
            ->willReturn($response);

        $this->assertTrue($this->resource->finishTaskcheck(456));
    }

    public function testActivateTaskcheck(): void
    {
        $response = $this->createSuccessResponse('{"result": "success"}');

        $this->client->expects($this->once())
            ->method('post')
            ->with('taskcheck/456/activate')
            ->willReturn($response);

        $this->assertTrue($this->resource->activateTaskcheck(456));
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
