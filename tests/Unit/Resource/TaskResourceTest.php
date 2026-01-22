<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Resource;

use Freelo\Sdk\Http\FreeloClient;
use Freelo\Sdk\Http\PaginatedResult;
use Freelo\Sdk\Http\Response;
use Freelo\Sdk\Http\ResponseParser;
use Freelo\Sdk\Model\Comment;
use Freelo\Sdk\Model\Task;
use Freelo\Sdk\Resource\TaskResource;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class TaskResourceTest extends TestCase
{
    private FreeloClient $client;
    private TaskResource $resource;

    protected function setUp(): void
    {
        $this->client = $this->createMock(FreeloClient::class);
        $parser = new ResponseParser();

        $this->client->method('getResponseParser')
            ->willReturn($parser);

        $this->resource = new TaskResource($this->client);
    }

    public function testGetAll(): void
    {
        $responseData = [
            'data' => [
                ['id' => 1, 'name' => 'Task 1'],
                ['id' => 2, 'name' => 'Task 2'],
            ],
            'current_page' => 1,
            'total_pages' => 1,
            'per_page' => 10,
            'total' => 2,
        ];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('get')
            ->with('all-tasks', [])
            ->willReturn($response);

        $result = $this->resource->getAll();

        $this->assertInstanceOf(PaginatedResult::class, $result);
        $this->assertCount(2, $result);
    }

    public function testListInTasklist(): void
    {
        $responseData = [
            ['id' => 1, 'name' => 'Task 1'],
            ['id' => 2, 'name' => 'Task 2'],
        ];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('get')
            ->with('project/123/tasklist/456/tasks', [])
            ->willReturn($response);

        $tasks = $this->resource->listInTasklist(123, 456);

        $this->assertCount(2, $tasks);
        $this->assertContainsOnlyInstancesOf(Task::class, $tasks);
    }

    public function testGetFinished(): void
    {
        $responseData = [
            'data' => [['id' => 1, 'name' => 'Finished Task']],
            'current_page' => 1,
            'total_pages' => 1,
            'per_page' => 10,
            'total' => 1,
        ];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('get')
            ->with('tasklist/456/finished-tasks', [])
            ->willReturn($response);

        $result = $this->resource->getFinished(456);

        $this->assertInstanceOf(PaginatedResult::class, $result);
        $this->assertCount(1, $result);
    }

    public function testGet(): void
    {
        $responseData = ['id' => 123, 'name' => 'Test Task'];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('get')
            ->with('task/123')
            ->willReturn($response);

        $task = $this->resource->get(123);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertSame(123, $task->id);
    }

    public function testCreate(): void
    {
        $createData = ['name' => 'New Task'];
        $responseData = ['id' => 999, 'name' => 'New Task'];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('post')
            ->with('project/123/tasklist/456/tasks', $createData)
            ->willReturn($response);

        $task = $this->resource->create(123, 456, $createData);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertSame(999, $task->id);
    }

    public function testCreateFromTemplate(): void
    {
        $createData = ['name' => 'From Template'];
        $responseData = ['id' => 888, 'name' => 'From Template'];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('post')
            ->with('task/create-from-template/100', $createData)
            ->willReturn($response);

        $task = $this->resource->createFromTemplate(100, $createData);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertSame(888, $task->id);
    }

    public function testUpdate(): void
    {
        $updateData = ['name' => 'Updated Task'];
        $responseData = ['id' => 123, 'name' => 'Updated Task'];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('post')
            ->with('task/123', $updateData)
            ->willReturn($response);

        $task = $this->resource->update(123, $updateData);

        $this->assertInstanceOf(Task::class, $task);
    }

    public function testDelete(): void
    {
        $response = $this->createSuccessResponse('', 204);

        $this->client->expects($this->once())
            ->method('delete')
            ->with('task/123')
            ->willReturn($response);

        $result = $this->resource->delete(123);

        $this->assertTrue($result);
    }

    public function testFinish(): void
    {
        $response = $this->createSuccessResponse('', 200);

        $this->client->expects($this->once())
            ->method('post')
            ->with('task/123/finish')
            ->willReturn($response);

        $result = $this->resource->finish(123);

        $this->assertTrue($result);
    }

    public function testActivate(): void
    {
        $response = $this->createSuccessResponse('', 200);

        $this->client->expects($this->once())
            ->method('post')
            ->with('task/123/activate')
            ->willReturn($response);

        $result = $this->resource->activate(123);

        $this->assertTrue($result);
    }

    public function testMove(): void
    {
        $response = $this->createSuccessResponse('', 200);

        $this->client->expects($this->once())
            ->method('post')
            ->with('task/123/move/456', [])
            ->willReturn($response);

        $result = $this->resource->move(123, 456);

        $this->assertTrue($result);
    }

    public function testAddComment(): void
    {
        $responseData = ['id' => 1, 'content' => 'Test comment'];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('post')
            ->with('task/123/comments', ['content' => 'Test comment'])
            ->willReturn($response);

        $comment = $this->resource->addComment(123, 'Test comment');

        $this->assertInstanceOf(Comment::class, $comment);
    }

    public function testSetDescription(): void
    {
        $responseData = ['id' => 1, 'content' => 'Description content'];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('post')
            ->with('task/123/description', ['content' => 'Description content'])
            ->willReturn($response);

        $comment = $this->resource->setDescription(123, 'Description content');

        $this->assertInstanceOf(Comment::class, $comment);
    }

    public function testSetTotalTimeEstimate(): void
    {
        $response = $this->createSuccessResponse('', 200);

        $this->client->expects($this->once())
            ->method('post')
            ->with('task/123/total-time-estimate', ['minutes' => 60])
            ->willReturn($response);

        $result = $this->resource->setTotalTimeEstimate(123, 60);

        $this->assertTrue($result);
    }

    public function testDeleteTotalTimeEstimate(): void
    {
        $response = $this->createSuccessResponse('', 204);

        $this->client->expects($this->once())
            ->method('delete')
            ->with('task/123/total-time-estimate')
            ->willReturn($response);

        $result = $this->resource->deleteTotalTimeEstimate(123);

        $this->assertTrue($result);
    }

    public function testListByProject(): void
    {
        $responseData = [
            'data' => [
                ['id' => 1, 'name' => 'Task 1'],
                ['id' => 2, 'name' => 'Task 2'],
            ],
            'current_page' => 1,
            'total_pages' => 1,
            'per_page' => 10,
            'total' => 2,
        ];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('get')
            ->with('all-tasks', ['projects_ids' => [123]])
            ->willReturn($response);

        $result = $this->resource->listByProject(123);

        $this->assertInstanceOf(PaginatedResult::class, $result);
        $this->assertCount(2, $result);
    }

    public function testListByProjectWithFilters(): void
    {
        $responseData = [
            'data' => [['id' => 1, 'name' => 'Task 1']],
            'current_page' => 1,
            'total_pages' => 1,
            'per_page' => 10,
            'total' => 1,
        ];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('get')
            ->with('all-tasks', ['state_id' => 1, 'projects_ids' => [123]])
            ->willReturn($response);

        $result = $this->resource->listByProject(123, ['state_id' => 1]);

        $this->assertInstanceOf(PaginatedResult::class, $result);
    }

    public function testListByWorker(): void
    {
        $responseData = [
            'data' => [['id' => 1, 'name' => 'Task 1']],
            'current_page' => 1,
            'total_pages' => 1,
            'per_page' => 10,
            'total' => 1,
        ];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('get')
            ->with('all-tasks', ['worker_id' => 42])
            ->willReturn($response);

        $result = $this->resource->listByWorker(42);

        $this->assertInstanceOf(PaginatedResult::class, $result);
    }

    public function testListOverdue(): void
    {
        $responseData = [
            'data' => [['id' => 1, 'name' => 'Overdue Task']],
            'current_page' => 1,
            'total_pages' => 1,
            'per_page' => 10,
            'total' => 1,
        ];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $today = date('Y-m-d');

        $this->client->expects($this->once())
            ->method('get')
            ->with('all-tasks', ['due_date_range' => ['date_to' => $today]])
            ->willReturn($response);

        $result = $this->resource->listOverdue();

        $this->assertInstanceOf(PaginatedResult::class, $result);
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
