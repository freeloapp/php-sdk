<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Resource;

use Freelo\Sdk\Http\FreeloClient;
use Freelo\Sdk\Http\PaginatedResult;
use Freelo\Sdk\Http\Response;
use Freelo\Sdk\Http\ResponseParser;
use Freelo\Sdk\Model\Project;
use Freelo\Sdk\Model\User;
use Freelo\Sdk\Resource\ProjectResource;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ProjectResourceTest extends TestCase
{
    private FreeloClient $client;
    private ResponseParser $parser;
    private ProjectResource $resource;

    protected function setUp(): void
    {
        $this->client = $this->createMock(FreeloClient::class);
        $this->parser = new ResponseParser();

        $this->client->method('getResponseParser')
            ->willReturn($this->parser);

        $this->resource = new ProjectResource($this->client);
    }

    public function testList(): void
    {
        $responseData = [
            ['id' => 1, 'name' => 'Project 1'],
            ['id' => 2, 'name' => 'Project 2'],
        ];

        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('get')
            ->with('projects', [])
            ->willReturn($response);

        $projects = $this->resource->list();

        $this->assertCount(2, $projects);
        $this->assertContainsOnlyInstancesOf(Project::class, $projects);
        $this->assertSame(1, $projects[0]->id);
        $this->assertSame('Project 1', $projects[0]->name);
    }

    public function testListWithFilters(): void
    {
        $filters = ['status' => 'active'];
        $response = $this->createSuccessResponse('[]');

        $this->client->expects($this->once())
            ->method('get')
            ->with('projects', $filters)
            ->willReturn($response);

        $projects = $this->resource->list($filters);

        $this->assertIsArray($projects);
    }

    public function testGetAll(): void
    {
        $responseData = [
            'data' => [
                ['id' => 1, 'name' => 'Project 1'],
                ['id' => 2, 'name' => 'Project 2'],
            ],
            'current_page' => 1,
            'total_pages' => 1,
            'per_page' => 10,
            'total' => 2,
        ];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('get')
            ->with('all-projects', [])
            ->willReturn($response);

        $result = $this->resource->getAll();

        $this->assertInstanceOf(PaginatedResult::class, $result);
        $this->assertCount(2, $result);
    }

    public function testGet(): void
    {
        $responseData = ['id' => 123, 'name' => 'Test Project'];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('get')
            ->with('project/123')
            ->willReturn($response);

        $project = $this->resource->get(123);

        $this->assertInstanceOf(Project::class, $project);
        $this->assertSame(123, $project->id);
        $this->assertSame('Test Project', $project->name);
    }

    public function testGetInvited(): void
    {
        $responseData = [
            'data' => [['id' => 2, 'name' => 'Invited Project']],
            'current_page' => 1,
            'total_pages' => 1,
            'per_page' => 10,
            'total' => 1,
        ];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('get')
            ->with('invited-projects', [])
            ->willReturn($response);

        $result = $this->resource->getInvited();

        $this->assertInstanceOf(PaginatedResult::class, $result);
        $this->assertCount(1, $result);
    }

    public function testGetArchived(): void
    {
        $responseData = [
            'data' => [['id' => 3, 'name' => 'Archived Project']],
            'current_page' => 1,
            'total_pages' => 1,
            'per_page' => 10,
            'total' => 1,
        ];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('get')
            ->with('archived-projects', [])
            ->willReturn($response);

        $result = $this->resource->getArchived();

        $this->assertInstanceOf(PaginatedResult::class, $result);
        $this->assertCount(1, $result);
    }

    public function testGetTemplates(): void
    {
        $responseData = [
            'data' => [['id' => 4, 'name' => 'Template Project']],
            'current_page' => 1,
            'total_pages' => 1,
            'per_page' => 10,
            'total' => 1,
        ];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('get')
            ->with('template-projects', [])
            ->willReturn($response);

        $result = $this->resource->getTemplates();

        $this->assertInstanceOf(PaginatedResult::class, $result);
        $this->assertCount(1, $result);
    }

    public function testGetUserProjects(): void
    {
        $responseData = [
            'data' => [['id' => 5, 'name' => 'User Project']],
            'current_page' => 1,
            'total_pages' => 1,
            'per_page' => 10,
            'total' => 1,
        ];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('get')
            ->with('user/456/all-projects', [])
            ->willReturn($response);

        $result = $this->resource->getUserProjects(456);

        $this->assertInstanceOf(PaginatedResult::class, $result);
        $this->assertCount(1, $result);
    }

    public function testCreate(): void
    {
        $createData = ['name' => 'New Project', 'currency_iso' => 'USD'];
        $responseData = ['id' => 999, 'name' => 'New Project', 'currency_iso' => 'USD'];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('post')
            ->with('projects', $createData)
            ->willReturn($response);

        $project = $this->resource->create($createData);

        $this->assertInstanceOf(Project::class, $project);
        $this->assertSame(999, $project->id);
        $this->assertSame('New Project', $project->name);
    }

    public function testDelete(): void
    {
        $response = $this->createSuccessResponse('', 204);

        $this->client->expects($this->once())
            ->method('delete')
            ->with('project/123')
            ->willReturn($response);

        $result = $this->resource->delete(123);

        $this->assertTrue($result);
    }

    public function testArchive(): void
    {
        $response = $this->createSuccessResponse('', 200);

        $this->client->expects($this->once())
            ->method('post')
            ->with('project/123/archive')
            ->willReturn($response);

        $result = $this->resource->archive(123);

        $this->assertTrue($result);
    }

    public function testActivate(): void
    {
        $response = $this->createSuccessResponse('', 200);

        $this->client->expects($this->once())
            ->method('post')
            ->with('project/123/activate')
            ->willReturn($response);

        $result = $this->resource->activate(123);

        $this->assertTrue($result);
    }

    public function testCreateFromTemplate(): void
    {
        $responseData = ['id' => 888, 'name' => 'From Template'];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('post')
            ->with('project/create-from-template/100', ['name' => 'New Name'])
            ->willReturn($response);

        $project = $this->resource->createFromTemplate(100, ['name' => 'New Name']);

        $this->assertInstanceOf(Project::class, $project);
        $this->assertSame(888, $project->id);
    }

    public function testGetWorkers(): void
    {
        $responseData = [
            'data' => [
                ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
                ['id' => 2, 'name' => 'Jane Doe', 'email' => 'jane@example.com'],
            ],
            'current_page' => 1,
            'total_pages' => 1,
            'per_page' => 10,
            'total' => 2,
        ];
        $response = $this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR));

        $this->client->expects($this->once())
            ->method('get')
            ->with('project/123/workers', [])
            ->willReturn($response);

        $result = $this->resource->getWorkers(123);

        $this->assertInstanceOf(PaginatedResult::class, $result);
        $this->assertCount(2, $result);
    }

    public function testRemoveWorkersByIds(): void
    {
        $response = $this->createSuccessResponse('', 200);

        $this->client->expects($this->once())
            ->method('post')
            ->with('project/123/remove-workers/by-ids', ['users_ids' => [1, 2, 3]])
            ->willReturn($response);

        $result = $this->resource->removeWorkersByIds(123, [1, 2, 3]);

        $this->assertTrue($result);
    }

    public function testRemoveWorkersByEmails(): void
    {
        $response = $this->createSuccessResponse('', 200);

        $this->client->expects($this->once())
            ->method('post')
            ->with('project/123/remove-workers/by-emails', ['users_emails' => ['a@b.com', 'c@d.com']])
            ->willReturn($response);

        $result = $this->resource->removeWorkersByEmails(123, ['a@b.com', 'c@d.com']);

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
