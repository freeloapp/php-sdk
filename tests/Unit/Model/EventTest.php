<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Model;

use Freelo\Sdk\Model\Event;
use Freelo\Sdk\Model\Project;
use Freelo\Sdk\Model\User;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    public function testFromArrayWithFullData(): void
    {
        $data = [
            'id' => 123,
            'type' => 'task_created',
            'date_action' => '2024-01-01T10:00:00Z',
            'author' => [
                'id' => 456,
                'fullname' => 'John Doe',
            ],
            'who' => [
                'id' => 789,
                'fullname' => 'Jane Smith',
            ],
            'task' => ['id' => 111, 'name' => 'Test Task'],
            'project' => ['id' => 222, 'name' => 'Test Project'],
            'due_date' => '2024-02-01T00:00:00Z',
        ];

        $event = Event::fromArray($data);

        $this->assertSame(123, $event->id);
        $this->assertSame('task_created', $event->type);
        $this->assertSame('2024-01-01T10:00:00Z', $event->dateAction);
        $this->assertInstanceOf(User::class, $event->author);
        $this->assertSame('John Doe', $event->author->fullname);
        $this->assertInstanceOf(User::class, $event->who);
        $this->assertSame(111, $event->getTaskId());
        $this->assertSame('Test Task', $event->getTaskName());
        $this->assertInstanceOf(Project::class, $event->project);
        $this->assertSame('2024-02-01T00:00:00Z', $event->dueDate);
    }

    public function testFromArrayWithMinimalData(): void
    {
        $data = [
            'id' => 456,
            'type' => 'comment_added',
        ];

        $event = Event::fromArray($data);

        $this->assertSame(456, $event->id);
        $this->assertSame('comment_added', $event->type);
        $this->assertNull($event->dateAction);
        $this->assertNull($event->author);
        $this->assertNull($event->project);
        $this->assertNull($event->getTaskId());
    }

    public function testFromArrayWithEmptyData(): void
    {
        $data = [];

        $event = Event::fromArray($data);

        $this->assertSame(0, $event->id);
        $this->assertSame('', $event->type);
    }

    public function testToArray(): void
    {
        $data = [
            'id' => 789,
            'type' => 'project_updated',
        ];

        $event = Event::fromArray($data);
        $result = $event->toArray();

        $this->assertSame($data, $result);
    }

    public function testConstructorWithAllParameters(): void
    {
        $author = User::fromArray(['id' => 100, 'fullname' => 'Event User']);
        $project = Project::fromArray(['id' => 200, 'name' => 'Test Project']);

        $event = new Event(
            id: 999,
            type: 'task_finished',
            dateAction: '2024-01-01T00:00:00Z',
            author: $author,
            who: null,
            comment: null,
            task: ['id' => 600, 'name' => 'Task Name'],
            taskCheck: null,
            tasklist: null,
            project: $project,
            document: null,
            file: null,
            dueDate: '2024-02-01T00:00:00Z',
            dueDateEnd: null,
            data: ['custom' => 'data'],
        );

        $this->assertSame(999, $event->id);
        $this->assertSame('task_finished', $event->type);
        $this->assertSame($author, $event->author);
        $this->assertSame(600, $event->getTaskId());
        $this->assertSame($project, $event->project);
    }
}
