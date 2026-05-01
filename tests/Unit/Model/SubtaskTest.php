<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Model;

use Freelo\Sdk\Model\Project;
use Freelo\Sdk\Model\State;
use Freelo\Sdk\Model\Subtask;
use Freelo\Sdk\Model\Tasklist;
use Freelo\Sdk\Model\User;
use Freelo\Sdk\Tests\Support\PragueTime;
use PHPUnit\Framework\TestCase;

class SubtaskTest extends TestCase
{
    public function testFromArrayWithFullData(): void
    {
        $data = [
            'id' => 123,
            'name' => 'Implement feature',
            'task_id' => 456,
            'date_add' => '2024-01-01T00:00:00',
            'due_date' => '2024-01-15T00:00:00',
            'due_date_end' => '2024-01-20T00:00:00',
            'count_comments' => 5,
            'count_subtasks' => 2,
            'author' => [
                'id' => 111,
                'fullname' => 'Jane Smith',
            ],
            'worker' => [
                'id' => 222,
                'fullname' => 'John Doe',
            ],
            'state' => [
                'id' => 1,
                'state' => 'active',
            ],
            'project' => [
                'id' => 333,
                'name' => 'Test Project',
            ],
            'tasklist' => [
                'id' => 444,
                'name' => 'Test Tasklist',
            ],
            'labels' => [
                ['uuid' => 'label-1', 'name' => 'Urgent', 'color' => 'red'],
            ],
        ];

        $subtask = Subtask::fromArray($data);

        $this->assertSame(123, $subtask->id);
        $this->assertSame('Implement feature', $subtask->name);
        $this->assertSame(456, $subtask->taskId);
        $this->assertEquals(PragueTime::utc('2024-01-01T00:00:00'), $subtask->dateAdd);
        $this->assertEquals(PragueTime::utc('2024-01-15T00:00:00'), $subtask->dueDate);
        $this->assertEquals(PragueTime::utc('2024-01-20T00:00:00'), $subtask->dueDateEnd);
        $this->assertSame(5, $subtask->countComments);
        $this->assertSame(2, $subtask->countSubtasks);
        $this->assertInstanceOf(User::class, $subtask->author);
        $this->assertInstanceOf(User::class, $subtask->worker);
        $this->assertSame('John Doe', $subtask->worker->fullname);
        $this->assertInstanceOf(State::class, $subtask->state);
        $this->assertInstanceOf(Project::class, $subtask->project);
        $this->assertInstanceOf(Tasklist::class, $subtask->tasklist);
        $this->assertCount(1, $subtask->labels);
    }

    public function testFromArrayWithMinimalData(): void
    {
        $data = [
            'id' => 456,
            'name' => 'Simple Subtask',
        ];

        $subtask = Subtask::fromArray($data);

        $this->assertSame(456, $subtask->id);
        $this->assertSame('Simple Subtask', $subtask->name);
        $this->assertNull($subtask->taskId);
        $this->assertNull($subtask->dateAdd);
        $this->assertNull($subtask->dueDate);
        $this->assertNull($subtask->state);
        $this->assertNull($subtask->worker);
    }

    public function testFromArrayWithEmptyData(): void
    {
        $data = [];

        $subtask = Subtask::fromArray($data);

        $this->assertSame(0, $subtask->id);
        $this->assertSame('', $subtask->name);
    }

    public function testToArray(): void
    {
        $data = [
            'id' => 789,
            'name' => 'Test Subtask',
            'date_add' => '2024-01-01T00:00:00',
        ];

        $subtask = Subtask::fromArray($data);
        $result = $subtask->toArray();

        $this->assertSame($data, $result);
    }

    public function testConstructorWithAllParameters(): void
    {
        $state = State::fromArray(['id' => 1, 'state' => 'active']);
        $worker = User::fromArray(['id' => 100, 'fullname' => 'Worker Name']);
        $author = User::fromArray(['id' => 101, 'fullname' => 'Author Name']);
        $project = Project::fromArray(['id' => 200, 'name' => 'Test Project']);
        $tasklist = Tasklist::fromArray(['id' => 300, 'name' => 'Test Tasklist']);

        $subtask = new Subtask(
            id: 999,
            name: 'Direct Construction',
            taskId: 500,
            dateAdd: PragueTime::utc('2024-01-01T00:00:00'),
            dueDate: PragueTime::utc('2024-01-15T00:00:00'),
            dueDateEnd: null,
            countComments: 10,
            countSubtasks: 3,
            author: $author,
            worker: $worker,
            state: $state,
            project: $project,
            tasklist: $tasklist,
            labels: [],
            data: ['custom' => 'data'],
        );

        $this->assertSame(999, $subtask->id);
        $this->assertSame('Direct Construction', $subtask->name);
        $this->assertSame(500, $subtask->taskId);
        $this->assertSame($state, $subtask->state);
        $this->assertSame($worker, $subtask->worker);
    }
}
