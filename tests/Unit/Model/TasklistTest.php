<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Model;

use Freelo\Sdk\Model\Currency;
use Freelo\Sdk\Model\Project;
use Freelo\Sdk\Model\State;
use Freelo\Sdk\Model\TaskLabel;
use Freelo\Sdk\Model\Tasklist;
use Freelo\Sdk\Tests\Support\PragueTime;
use PHPUnit\Framework\TestCase;

class TasklistTest extends TestCase
{
    public function testFromArrayWithFullData(): void
    {
        $data = [
            'id' => 123,
            'name' => 'Development Tasks',
            'date_add' => '2024-01-01T00:00:00',
            'date_edited_at' => '2024-01-02T00:00:00',
            'state' => [
                'id' => 1,
                'state' => 'active',
            ],
            'project' => [
                'id' => 456,
                'name' => 'Parent Project',
            ],
            'budget' => [
                'amount' => '100000',
                'currency' => 'CZK',
            ],
            'real_minutes_spent' => 480,
            'real_cost' => [
                'amount' => '50000',
                'currency' => 'CZK',
            ],
            'labels' => [
                ['id' => 1, 'name' => 'Urgent', 'color' => 'red'],
                ['id' => 2, 'name' => 'Bug', 'color' => 'orange'],
            ],
        ];

        $tasklist = Tasklist::fromArray($data);

        $this->assertSame(123, $tasklist->id);
        $this->assertSame('Development Tasks', $tasklist->name);
        $this->assertEquals(PragueTime::utc('2024-01-01T00:00:00'), $tasklist->dateAdd);
        $this->assertEquals(PragueTime::utc('2024-01-02T00:00:00'), $tasklist->dateEditedAt);
        $this->assertInstanceOf(State::class, $tasklist->state);
        $this->assertTrue($tasklist->state->isActive());
        $this->assertInstanceOf(Project::class, $tasklist->project);
        $this->assertSame(456, $tasklist->project->id);
        $this->assertInstanceOf(Currency::class, $tasklist->budget);
        $this->assertSame('100000', $tasklist->budget->amount);
        $this->assertSame(480, $tasklist->realMinutesSpent);
        $this->assertInstanceOf(Currency::class, $tasklist->realCost);
        $this->assertCount(2, $tasklist->labels);
        $this->assertInstanceOf(TaskLabel::class, $tasklist->labels[0]);
    }

    public function testFromArrayWithMinimalData(): void
    {
        $data = [
            'id' => 456,
            'name' => 'Minimal Tasklist',
        ];

        $tasklist = Tasklist::fromArray($data);

        $this->assertSame(456, $tasklist->id);
        $this->assertSame('Minimal Tasklist', $tasklist->name);
        $this->assertNull($tasklist->dateAdd);
        $this->assertNull($tasklist->state);
        $this->assertNull($tasklist->project);
        $this->assertNull($tasklist->budget);
        $this->assertNull($tasklist->realMinutesSpent);
        $this->assertEmpty($tasklist->labels);
    }

    public function testFromArrayWithEmptyData(): void
    {
        $data = [];

        $tasklist = Tasklist::fromArray($data);

        $this->assertSame(0, $tasklist->id);
        $this->assertSame('', $tasklist->name);
    }

    public function testToArray(): void
    {
        $data = [
            'id' => 789,
            'name' => 'Test Tasklist',
            'date_add' => '2024-01-01T00:00:00',
        ];

        $tasklist = Tasklist::fromArray($data);
        $result = $tasklist->toArray();

        $this->assertSame($data, $result);
    }

    public function testConstructorWithAllParameters(): void
    {
        $state = State::fromArray(['id' => 1, 'state' => 'active']);
        $project = Project::fromArray(['id' => 100, 'name' => 'Test Project']);
        $budget = Currency::fromArray(['amount' => '200000', 'currency' => 'EUR']);

        $tasklist = new Tasklist(
            id: 999,
            name: 'Direct Construction',
            dateAdd: PragueTime::utc('2024-01-01T00:00:00'),
            dateEditedAt: PragueTime::utc('2024-01-02T00:00:00'),
            state: $state,
            project: $project,
            budget: $budget,
            realMinutesSpent: 120,
            data: ['custom' => 'data'],
        );

        $this->assertSame(999, $tasklist->id);
        $this->assertSame('Direct Construction', $tasklist->name);
        $this->assertSame($state, $tasklist->state);
        $this->assertSame($project, $tasklist->project);
        $this->assertSame(120, $tasklist->realMinutesSpent);
    }
}
