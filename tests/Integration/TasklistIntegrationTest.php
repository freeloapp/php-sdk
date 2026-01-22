<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Integration;

use Freelo\Sdk\Model\Tasklist;

/**
 * Integration tests for Tasklist resource
 */
class TasklistIntegrationTest extends IntegrationTestCase
{
    public function testCanGetAllTasklistsPaginated(): void
    {
        $result = $this->getFreelo()->tasklists()->getAll(['p' => 0]);

        $this->assertGreaterThanOrEqual(0, $result->getTotal());
        $this->assertSame(0, $result->getPage());
    }

    public function testCanListTasklistsInProject(): void
    {
        // First get a project
        $projectResult = $this->getFreelo()->projects()->getAll(['p' => 0]);

        if ($projectResult->isEmpty()) {
            $this->markTestSkipped('No projects available to test');
        }

        $project = $projectResult->first();

        // Then get tasklists for that project
        $tasklists = $this->getFreelo()->tasklists()->listInProject($project->id);

        $this->assertIsArray($tasklists);

        if (!empty($tasklists)) {
            $this->assertInstanceOf(Tasklist::class, $tasklists[0]);
        }
    }

    public function testCanGetTasklistById(): void
    {
        $result = $this->getFreelo()->tasklists()->getAll(['p' => 0]);

        if ($result->isEmpty()) {
            $this->markTestSkipped('No tasklists available to test');
        }

        $firstTasklist = $result->first();
        $tasklist = $this->getFreelo()->tasklists()->get($firstTasklist->id);

        $this->assertInstanceOf(Tasklist::class, $tasklist);
        $this->assertSame($firstTasklist->id, $tasklist->id);
    }
}
