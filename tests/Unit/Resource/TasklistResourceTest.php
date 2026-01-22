<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Resource;

use Freelo\Sdk\Http\FreeloClient;
use Freelo\Sdk\Http\PaginatedResult;
use Freelo\Sdk\Http\Response;
use Freelo\Sdk\Http\ResponseParser;
use Freelo\Sdk\Model\Tasklist;
use Freelo\Sdk\Model\User;
use Freelo\Sdk\Resource\TasklistResource;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class TasklistResourceTest extends TestCase
{
    private FreeloClient $client;
    private TasklistResource $resource;

    protected function setUp(): void
    {
        $this->client = $this->createMock(FreeloClient::class);
        $parser = new ResponseParser();

        $this->client->method('getResponseParser')
            ->willReturn($parser);

        $this->resource = new TasklistResource($this->client);
    }

    public function testGetAll(): void
    {
        $responseData = [
            'data' => [
                ['id' => 1, 'name' => 'List 1'],
                ['id' => 2, 'name' => 'List 2'],
            ],
            'current_page' => 1,
            'total_pages' => 1,
            'per_page' => 10,
            'total' => 2,
        ];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('get')
            ->with('all-tasklists', [])
            ->willReturn($response);

        $result = $this->resource->getAll();

        $this->assertInstanceOf(PaginatedResult::class, $result);
        $this->assertCount(2, $result);
    }

    public function testList(): void
    {
        $responseData = [
            ['id' => 1, 'name' => 'List 1'],
            ['id' => 2, 'name' => 'List 2'],
        ];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('get')
            ->with('project/123/tasklists')
            ->willReturn($response);

        $lists = $this->resource->list(123);

        $this->assertCount(2, $lists);
        $this->assertContainsOnlyInstancesOf(Tasklist::class, $lists);
    }

    public function testGet(): void
    {
        $responseData = ['id' => 456, 'name' => 'Test List'];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('get')
            ->with('tasklist/456')
            ->willReturn($response);

        $list = $this->resource->get(456);

        $this->assertInstanceOf(Tasklist::class, $list);
        $this->assertSame(456, $list->id);
    }

    public function testCreate(): void
    {
        $createData = ['name' => 'New List'];
        $responseData = ['id' => 999, 'name' => 'New List'];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('post')
            ->with('project/123/tasklists', $createData)
            ->willReturn($response);

        $list = $this->resource->create(123, ['name' => 'New List']);

        $this->assertInstanceOf(Tasklist::class, $list);
    }

    public function testCreateFromTemplate(): void
    {
        $createData = ['name' => 'From Template'];
        $responseData = ['id' => 888, 'name' => 'From Template'];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('post')
            ->with('tasklist/create-from-template/100', $createData)
            ->willReturn($response);

        $list = $this->resource->createFromTemplate(100, $createData);

        $this->assertInstanceOf(Tasklist::class, $list);
        $this->assertSame(888, $list->id);
    }

    public function testGetAssignableWorkers(): void
    {
        $responseData = [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
            ['id' => 2, 'name' => 'Jane Doe', 'email' => 'jane@example.com'],
        ];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('get')
            ->with('project/123/tasklist/456/assignable-workers')
            ->willReturn($response);

        $workers = $this->resource->getAssignableWorkers(123, 456);

        $this->assertCount(2, $workers);
        $this->assertContainsOnlyInstancesOf(User::class, $workers);
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
