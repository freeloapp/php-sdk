<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Model;

use Freelo\Sdk\Model\Comment;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    public function testFromArrayWithFullData(): void
    {
        $data = [
            'id' => 123,
            'uuid' => 'abc-123-def',
            'content' => 'Test comment',
            'task' => ['id' => 456, 'name' => 'Test Task'],
            'date_add' => '2024-01-01T00:00:00Z',
            'date_edited_at' => '2024-01-02T00:00:00Z',
            'is_description' => false,
        ];

        $comment = Comment::fromArray($data);

        $this->assertSame(123, $comment->id);
        $this->assertSame('abc-123-def', $comment->uuid);
        $this->assertSame('Test comment', $comment->content);
        $this->assertSame(456, $comment->getTaskId());
        $this->assertSame('Test Task', $comment->getTaskName());
        $this->assertSame('2024-01-01T00:00:00Z', $comment->dateAdd);
        $this->assertSame('2024-01-02T00:00:00Z', $comment->dateEditedAt);
        $this->assertFalse($comment->isDescription);
    }

    public function testFromArrayWithMinimalData(): void
    {
        $data = [
            'id' => 789,
            'content' => 'Minimal',
        ];

        $comment = Comment::fromArray($data);

        $this->assertSame(789, $comment->id);
        $this->assertSame('Minimal', $comment->content);
        $this->assertNull($comment->task);
        $this->assertNull($comment->getTaskId());
        $this->assertNull($comment->dateAdd);
    }

    public function testToArray(): void
    {
        $data = ['id' => 999, 'content' => 'Test'];
        $comment = Comment::fromArray($data);

        $this->assertSame($data, $comment->toArray());
    }
}
