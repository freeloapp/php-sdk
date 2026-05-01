<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Model;

use Freelo\Sdk\Model\Task;
use Freelo\Sdk\Tests\Support\PragueTime;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testFromArrayWithFullData(): void
    {
        $data = [
            'id' => 123,
            'name' => 'Test Task',
            'date_add' => '2024-01-01T00:00:00',
            'date_edited_at' => '2024-01-02T00:00:00',
            'due_date' => '2024-12-31',
            'priority_enum' => 'high',
            'minutes' => 60,
        ];

        $task = Task::fromArray($data);

        $this->assertSame(123, $task->id);
        $this->assertSame('Test Task', $task->name);
        $this->assertEquals(PragueTime::utc('2024-01-01T00:00:00'), $task->dateAdd);
        $this->assertEquals(PragueTime::utc('2024-01-02T00:00:00'), $task->dateEditedAt);
        $this->assertEquals(PragueTime::utc('2024-12-31'), $task->dueDate);
        $this->assertSame('high', $task->priorityEnum);
        $this->assertSame(60, $task->minutes);
    }

    public function testFromArrayWithMinimalData(): void
    {
        $data = [
            'id' => 456,
            'name' => 'Minimal Task',
        ];

        $task = Task::fromArray($data);

        $this->assertSame(456, $task->id);
        $this->assertSame('Minimal Task', $task->name);
        $this->assertNull($task->dateAdd);
        $this->assertNull($task->dueDate);
        $this->assertNull($task->priorityEnum);
        $this->assertNull($task->author);
        $this->assertNull($task->state);
    }

    public function testToArray(): void
    {
        $data = [
            'id' => 789,
            'name' => 'Test Task',
            'priority_enum' => 'normal',
        ];

        $task = Task::fromArray($data);
        $result = $task->toArray();

        $this->assertSame($data, $result);
    }

    public function testConstructorWithAllParameters(): void
    {
        $task = new Task(
            id: 999,
            name: 'Direct Construction',
            dateAdd: PragueTime::utc('2024-01-01T00:00:00'),
            dateEditedAt: PragueTime::utc('2024-01-02T00:00:00'),
            dueDate: PragueTime::utc('2024-12-31'),
            priorityEnum: 'high',
            data: ['custom' => 'data'],
        );

        $this->assertSame(999, $task->id);
        $this->assertSame('Direct Construction', $task->name);
        $this->assertEquals(PragueTime::utc('2024-12-31'), $task->dueDate);
        $this->assertSame('high', $task->priorityEnum);
    }
}
