<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Integration;

use Freelo\Sdk\Model\Task;

/**
 * Integration tests for Task resource
 */
class TaskIntegrationTest extends IntegrationTestCase
{
    public function testCanGetAllTasksPaginated(): void
    {
        $result = $this->getFreelo()->tasks()->getAll(['p' => 0]);

        $this->assertGreaterThanOrEqual(0, $result->getTotal());
        $this->assertSame(0, $result->getPage());
    }

    public function testCanGetTasksByProjectId(): void
    {
        // First get a project
        $projectResult = $this->getFreelo()->projects()->getAll(['p' => 0]);

        if ($projectResult->isEmpty()) {
            $this->markTestSkipped('No projects available to test');
        }

        $project = $projectResult->first();

        // Then get tasks for that project
        $result = $this->getFreelo()->tasks()->getAll([
            'projects_ids' => [$project->id],
            'p' => 0,
        ]);

        $this->assertGreaterThanOrEqual(0, $result->getTotal());
    }

    public function testCanGetTaskById(): void
    {
        $result = $this->getFreelo()->tasks()->getAll(['p' => 0]);

        if ($result->isEmpty()) {
            $this->markTestSkipped('No tasks available to test');
        }

        $firstTask = $result->first();
        $task = $this->getFreelo()->tasks()->get($firstTask->id);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertSame($firstTask->id, $task->id);
    }
}
