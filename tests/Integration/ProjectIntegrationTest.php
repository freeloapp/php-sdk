<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Integration;

use Freelo\Sdk\Model\Project;

/**
 * Integration tests for Project resource
 */
class ProjectIntegrationTest extends IntegrationTestCase
{
    public function testCanListProjects(): void
    {
        $projects = $this->getFreelo()->projects()->list();

        $this->assertIsArray($projects);

        if (!empty($projects)) {
            $this->assertInstanceOf(Project::class, $projects[0]);
        }
    }

    public function testCanGetOwnedProjects(): void
    {
        $projects = $this->getFreelo()->projects()->getOwned();

        $this->assertIsArray($projects);

        if (!empty($projects)) {
            $this->assertInstanceOf(Project::class, $projects[0]);
        }
    }

    public function testCanGetAllProjectsPaginated(): void
    {
        $result = $this->getFreelo()->projects()->getAll(['p' => 0]);

        $this->assertGreaterThanOrEqual(0, $result->getTotal());
        $this->assertSame(0, $result->getPage());
    }

    public function testCanGetProjectById(): void
    {
        $result = $this->getFreelo()->projects()->getAll(['p' => 0]);

        if ($result->isEmpty()) {
            $this->markTestSkipped('No projects available to test');
        }

        $firstProject = $result->first();
        $project = $this->getFreelo()->projects()->get($firstProject->id);

        $this->assertInstanceOf(Project::class, $project);
        $this->assertSame($firstProject->id, $project->id);
    }
}
