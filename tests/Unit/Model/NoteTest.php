<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Model;

use Freelo\Sdk\Model\Note;
use Freelo\Sdk\Model\User;
use PHPUnit\Framework\TestCase;

class NoteTest extends TestCase
{
    public function testFromArrayWithFullData(): void
    {
        $data = [
            'id' => 123,
            'content' => 'This is a project note with important information.',
            'date_add' => '2024-01-01T00:00:00Z',
            'date_edited_at' => '2024-01-02T00:00:00Z',
            'author' => [
                'id' => 456,
                'fullname' => 'John Doe',
            ],
            'project_id' => 789,
        ];

        $note = Note::fromArray($data);

        $this->assertSame(123, $note->id);
        $this->assertSame('This is a project note with important information.', $note->content);
        $this->assertSame('2024-01-01T00:00:00Z', $note->dateAdd);
        $this->assertSame('2024-01-02T00:00:00Z', $note->dateEditedAt);
        $this->assertInstanceOf(User::class, $note->author);
        $this->assertSame('John Doe', $note->author->fullname);
        $this->assertSame(789, $note->projectId);
    }

    public function testFromArrayWithMinimalData(): void
    {
        $data = [
            'id' => 456,
        ];

        $note = Note::fromArray($data);

        $this->assertSame(456, $note->id);
        $this->assertNull($note->content);
        $this->assertNull($note->dateAdd);
        $this->assertNull($note->author);
        $this->assertNull($note->projectId);
    }

    public function testFromArrayWithEmptyData(): void
    {
        $data = [];

        $note = Note::fromArray($data);

        $this->assertSame(0, $note->id);
        $this->assertNull($note->content);
    }

    public function testToArray(): void
    {
        $data = [
            'id' => 789,
            'content' => 'Test note',
            'project_id' => 111,
        ];

        $note = Note::fromArray($data);
        $result = $note->toArray();

        $this->assertSame($data, $result);
    }

    public function testConstructorWithAllParameters(): void
    {
        $author = User::fromArray(['id' => 100, 'fullname' => 'Author Name']);

        $note = new Note(
            id: 999,
            content: 'Direct construction test',
            dateAdd: '2024-01-01T00:00:00Z',
            dateEditedAt: '2024-01-02T00:00:00Z',
            author: $author,
            projectId: 500,
            data: ['custom' => 'data'],
        );

        $this->assertSame(999, $note->id);
        $this->assertSame('Direct construction test', $note->content);
        $this->assertSame($author, $note->author);
        $this->assertSame(500, $note->projectId);
    }
}
