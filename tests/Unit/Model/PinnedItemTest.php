<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Model;

use Freelo\Sdk\Model\PinnedItem;
use PHPUnit\Framework\TestCase;

class PinnedItemTest extends TestCase
{
    public function testFromArrayWithFullData(): void
    {
        $data = [
            'id' => 123,
            'type' => 'task',
            'name' => 'Important Task',
            'entity_id' => 456,
        ];

        $item = PinnedItem::fromArray($data);

        $this->assertSame(123, $item->id);
        $this->assertSame('task', $item->type);
        $this->assertSame('Important Task', $item->name);
        $this->assertSame(456, $item->entityId);
        $this->assertSame($data, $item->data);
    }

    public function testFromArrayWithMinimalData(): void
    {
        $data = [
            'id' => 456,
            'type' => 'project',
        ];

        $item = PinnedItem::fromArray($data);

        $this->assertSame(456, $item->id);
        $this->assertSame('project', $item->type);
        $this->assertNull($item->name);
        $this->assertNull($item->entityId);
    }

    public function testFromArrayWithEmptyData(): void
    {
        $data = [];

        $item = PinnedItem::fromArray($data);

        $this->assertSame(0, $item->id);
        $this->assertSame('', $item->type);
    }

    public function testIsTask(): void
    {
        $taskItem = PinnedItem::fromArray(['id' => 1, 'type' => 'task']);
        $projectItem = PinnedItem::fromArray(['id' => 2, 'type' => 'project']);

        $this->assertTrue($taskItem->isTask());
        $this->assertFalse($projectItem->isTask());
    }

    public function testIsProject(): void
    {
        $projectItem = PinnedItem::fromArray(['id' => 1, 'type' => 'project']);
        $taskItem = PinnedItem::fromArray(['id' => 2, 'type' => 'task']);

        $this->assertTrue($projectItem->isProject());
        $this->assertFalse($taskItem->isProject());
    }

    public function testToArray(): void
    {
        $data = [
            'id' => 789,
            'type' => 'task',
            'name' => 'Pinned Task',
        ];

        $item = PinnedItem::fromArray($data);
        $result = $item->toArray();

        $this->assertSame($data, $result);
    }

    public function testConstructorWithAllParameters(): void
    {
        $item = new PinnedItem(
            id: 999,
            type: 'project',
            name: 'Direct Construction',
            entityId: 500,
            data: ['custom' => 'data'],
        );

        $this->assertSame(999, $item->id);
        $this->assertSame('project', $item->type);
        $this->assertTrue($item->isProject());
        $this->assertSame(500, $item->entityId);
    }
}
