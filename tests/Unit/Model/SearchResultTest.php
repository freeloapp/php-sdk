<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Model;

use Freelo\Sdk\Model\SearchResult;
use PHPUnit\Framework\TestCase;

class SearchResultTest extends TestCase
{
    public function testFromArrayWithFullData(): void
    {
        $data = [
            'id' => 123,
            'type' => 'task',
            'name' => 'Implement feature X',
            'content' => 'Detailed description of the task',
            'project_id' => 456,
            'project_name' => 'Main Project',
        ];

        $result = SearchResult::fromArray($data);

        $this->assertSame(123, $result->id);
        $this->assertSame('task', $result->type);
        $this->assertSame('Implement feature X', $result->name);
        $this->assertSame('Detailed description of the task', $result->content);
        $this->assertSame(456, $result->projectId);
        $this->assertSame('Main Project', $result->projectName);
        $this->assertSame($data, $result->data);
    }

    public function testFromArrayWithMinimalData(): void
    {
        $data = [
            'id' => 456,
            'type' => 'project',
        ];

        $result = SearchResult::fromArray($data);

        $this->assertSame(456, $result->id);
        $this->assertSame('project', $result->type);
        $this->assertNull($result->name);
        $this->assertNull($result->content);
        $this->assertNull($result->projectId);
    }

    public function testFromArrayWithEmptyData(): void
    {
        $data = [];

        $result = SearchResult::fromArray($data);

        $this->assertSame(0, $result->id);
        $this->assertSame('', $result->type);
    }

    public function testIsTask(): void
    {
        $taskResult = SearchResult::fromArray(['id' => 1, 'type' => 'task']);
        $projectResult = SearchResult::fromArray(['id' => 2, 'type' => 'project']);

        $this->assertTrue($taskResult->isTask());
        $this->assertFalse($projectResult->isTask());
    }

    public function testIsProject(): void
    {
        $projectResult = SearchResult::fromArray(['id' => 1, 'type' => 'project']);
        $taskResult = SearchResult::fromArray(['id' => 2, 'type' => 'task']);

        $this->assertTrue($projectResult->isProject());
        $this->assertFalse($taskResult->isProject());
    }

    public function testIsComment(): void
    {
        $commentResult = SearchResult::fromArray(['id' => 1, 'type' => 'comment']);
        $taskResult = SearchResult::fromArray(['id' => 2, 'type' => 'task']);

        $this->assertTrue($commentResult->isComment());
        $this->assertFalse($taskResult->isComment());
    }

    public function testIsCommentMatchesUnderscoreCommentTypes(): void
    {
        foreach (['task_comment', 'note_comment', 'file_comment', 'link_comment'] as $type) {
            $result = SearchResult::fromArray(['id' => 1, 'type' => $type]);
            $this->assertTrue($result->isComment(), "Type {$type} should be a comment");
        }

        $fileResult = SearchResult::fromArray(['id' => 2, 'type' => 'file']);
        $this->assertFalse($fileResult->isComment());
    }

    public function testIsTaskcheck(): void
    {
        $taskcheckResult = SearchResult::fromArray(['id' => 1, 'type' => 'taskcheck']);
        $taskResult = SearchResult::fromArray(['id' => 2, 'type' => 'task']);

        $this->assertTrue($taskcheckResult->isTaskcheck());
        $this->assertFalse($taskResult->isTaskcheck());
    }

    public function testToArray(): void
    {
        $data = [
            'id' => 789,
            'type' => 'task',
            'name' => 'Test Task',
        ];

        $result = SearchResult::fromArray($data);
        $resultArray = $result->toArray();

        $this->assertSame($data, $resultArray);
    }

    public function testConstructorWithAllParameters(): void
    {
        $result = new SearchResult(
            id: 999,
            type: 'comment',
            name: null,
            content: 'This is a comment content',
            projectId: 100,
            projectName: 'Test Project',
            data: ['custom' => 'data'],
        );

        $this->assertSame(999, $result->id);
        $this->assertSame('comment', $result->type);
        $this->assertTrue($result->isComment());
        $this->assertSame(100, $result->projectId);
    }
}
