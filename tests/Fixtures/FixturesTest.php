<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Fixtures;

use Freelo\Sdk\Model\Comment;
use Freelo\Sdk\Model\Project;
use Freelo\Sdk\Model\Task;
use Freelo\Sdk\Model\User;
use Freelo\Sdk\Model\WorkReport;

/**
 * Tests to ensure fixtures are valid and can be loaded
 */
class FixturesTest extends TestCase
{
    public function testCanLoadProjectsListFixture(): void
    {
        $data = $this->loadFixture('projects-list');

        $this->assertIsArray($data);
        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertArrayHasKey('name', $data[0]);

        // Verify we can create models from fixture data
        $project = Project::fromArray($data[0]);
        $this->assertSame(123, $project->id);
        $this->assertSame('Project Alpha', $project->name);
    }

    public function testCanLoadAllProjectsPaginatedFixture(): void
    {
        $data = $this->loadFixture('all-projects-paginated');

        $this->assertIsArray($data);
        $this->assertArrayHasKey('total', $data);
        $this->assertArrayHasKey('count', $data);
        $this->assertArrayHasKey('page', $data);
        $this->assertArrayHasKey('per_page', $data);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('projects', $data['data']);

        $this->assertSame(150, $data['total']);
        $this->assertSame(20, $data['count']);
    }

    public function testCanLoadProjectDetailFixture(): void
    {
        $data = $this->loadFixture('project-detail');

        $project = Project::fromArray($data);

        $this->assertSame(123, $project->id);
        $this->assertNotNull($project->owner);
        $this->assertSame('John Doe', $project->owner->fullname);
    }

    public function testCanLoadTasksListFixture(): void
    {
        $data = $this->loadFixture('tasks-list');

        $this->assertIsArray($data);
        $this->assertNotEmpty($data);

        $task = Task::fromArray($data[0]);
        $this->assertSame(1001, $task->id);
        $this->assertSame('Implement user authentication', $task->name);
    }

    public function testCanLoadTaskDetailFixture(): void
    {
        $data = $this->loadFixture('task-detail');

        $task = Task::fromArray($data);

        $this->assertSame(1001, $task->id);
        $this->assertSame('h', $task->priorityEnum);
    }

    public function testCanLoadCommentsListFixture(): void
    {
        $data = $this->loadFixture('comments-list');

        $this->assertIsArray($data);
        $this->assertCount(2, $data);

        $comment = Comment::fromArray($data[0]);
        $this->assertSame(3001, $comment->id);
    }

    public function testCanLoadUsersListFixture(): void
    {
        $data = $this->loadFixture('users-list');

        $this->assertIsArray($data);
        $this->assertCount(3, $data);

        $user = User::fromArray($data[0]);
        $this->assertSame(456, $user->id);
        $this->assertSame('John Doe', $user->fullname);
        $this->assertNotNull($user->hourRate);
    }

    public function testCanLoadWorkReportsListFixture(): void
    {
        $data = $this->loadFixture('work-reports-list');

        $this->assertIsArray($data);
        $this->assertCount(2, $data);

        $report = WorkReport::fromArray($data[0]);
        $this->assertSame(5001, $report->id);
        $this->assertSame(120, $report->minutes);
        $this->assertSame(2.0, $report->getHours());
    }

    public function testCanLoadErrorNotFoundFixture(): void
    {
        $data = $this->loadFixture('error-not-found');

        $this->assertArrayHasKey('error', $data);
        $this->assertSame('not_found', $data['error']);
    }

    public function testCanLoadErrorValidationFixture(): void
    {
        $data = $this->loadFixture('error-validation');

        $this->assertArrayHasKey('error', $data);
        $this->assertArrayHasKey('errors', $data);
        $this->assertSame('validation_error', $data['error']);
    }

    public function testCanLoadErrorRateLimitFixture(): void
    {
        $data = $this->loadFixture('error-rate-limit');

        $this->assertArrayHasKey('error', $data);
        $this->assertArrayHasKey('retry_after', $data);
        $this->assertSame(60, $data['retry_after']);
    }

    public function testCanLoadFixtureRaw(): void
    {
        $raw = $this->loadFixtureRaw('projects-list');

        $this->assertIsString($raw);
        $this->assertStringContainsString('Project Alpha', $raw);
    }

    public function testGetFixturePath(): void
    {
        $path = $this->getFixturePath('projects-list');

        $this->assertStringEndsWith('responses/projects-list.json', $path);
        $this->assertFileExists($path);
    }

    public function testLoadFixtureThrowsExceptionForMissingFile(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Fixture file not found');

        $this->loadFixture('non-existent-fixture');
    }
}
