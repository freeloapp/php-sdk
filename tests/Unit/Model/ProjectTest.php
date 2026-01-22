<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Model;

use Freelo\Sdk\Model\Project;
use PHPUnit\Framework\TestCase;

class ProjectTest extends TestCase
{
    public function testFromArrayWithFullData(): void
    {
        $data = [
            'id' => 123,
            'name' => 'Test Project',
            'date_add' => '2024-01-01T00:00:00Z',
            'date_edited_at' => '2024-01-02T00:00:00Z',
            'extra_field' => 'extra_value',
        ];

        $project = Project::fromArray($data);

        $this->assertSame(123, $project->id);
        $this->assertSame('Test Project', $project->name);
        $this->assertSame('2024-01-01T00:00:00Z', $project->dateAdd);
        $this->assertSame('2024-01-02T00:00:00Z', $project->dateEditedAt);
        $this->assertSame($data, $project->data);
    }

    public function testFromArrayWithMinimalData(): void
    {
        $data = [
            'id' => 456,
            'name' => 'Minimal Project',
        ];

        $project = Project::fromArray($data);

        $this->assertSame(456, $project->id);
        $this->assertSame('Minimal Project', $project->name);
        $this->assertNull($project->dateAdd);
        $this->assertNull($project->dateEditedAt);
        $this->assertNull($project->owner);
        $this->assertNull($project->state);
    }

    public function testFromArrayWithEmptyData(): void
    {
        $data = [];

        $project = Project::fromArray($data);

        $this->assertSame(0, $project->id);
        $this->assertSame('', $project->name);
        $this->assertNull($project->dateAdd);
    }

    public function testToArray(): void
    {
        $data = [
            'id' => 789,
            'name' => 'Test Project',
            'date_add' => '2024-01-01T00:00:00Z',
            'custom_field' => 'custom_value',
        ];

        $project = Project::fromArray($data);
        $result = $project->toArray();

        $this->assertSame($data, $result);
    }

    public function testConstructorWithAllParameters(): void
    {
        $project = new Project(
            id: 999,
            name: 'Direct Construction',
            dateAdd: '2024-01-01T00:00:00Z',
            dateEditedAt: '2024-01-02T00:00:00Z',
            data: ['custom' => 'data'],
        );

        $this->assertSame(999, $project->id);
        $this->assertSame('Direct Construction', $project->name);
        $this->assertSame('2024-01-01T00:00:00Z', $project->dateAdd);
        $this->assertSame('2024-01-02T00:00:00Z', $project->dateEditedAt);
    }
}
