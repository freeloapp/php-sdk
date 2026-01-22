<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Model;

use Freelo\Sdk\Model\File;
use Freelo\Sdk\Model\State;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testFromArrayWithFullData(): void
    {
        $data = [
            'id' => 123,
            'uuid' => 'abc-123-def',
            'filename' => 'document.pdf',
            'size' => 1024,
            'caption' => 'Important Document',
            'description' => 'A test document',
            'mime_type' => 'application/pdf',
            'date_add' => '2024-01-01T00:00:00Z',
            'date_edited_at' => '2024-01-02T00:00:00Z',
            'state' => ['id' => 1, 'state' => 'active'],
        ];

        $file = File::fromArray($data);

        $this->assertSame('abc-123-def', $file->uuid);
        $this->assertSame('document.pdf', $file->filename);
        $this->assertSame(123, $file->id);
        $this->assertSame(1024, $file->size);
        $this->assertSame('Important Document', $file->caption);
        $this->assertSame('A test document', $file->description);
        $this->assertSame('application/pdf', $file->mimeType);
        $this->assertSame('2024-01-01T00:00:00Z', $file->dateAdd);
        $this->assertSame('2024-01-02T00:00:00Z', $file->dateEditedAt);
        $this->assertInstanceOf(State::class, $file->state);
    }

    public function testFromArrayWithMinimalData(): void
    {
        $data = [
            'uuid' => 'xyz-456',
            'filename' => 'file.txt',
        ];

        $file = File::fromArray($data);

        $this->assertSame('xyz-456', $file->uuid);
        $this->assertSame('file.txt', $file->filename);
        $this->assertNull($file->id);
        $this->assertNull($file->size);
    }

    public function testFromArrayWithNameFallback(): void
    {
        $data = [
            'uuid' => 'legacy-uuid-123',
            'name' => 'legacy.pdf',
        ];

        $file = File::fromArray($data);

        $this->assertSame('legacy-uuid-123', $file->uuid);
        $this->assertSame('legacy.pdf', $file->filename);
    }

    public function testGetDisplayName(): void
    {
        $fileWithCaption = File::fromArray([
            'uuid' => 'test-1',
            'filename' => 'file.pdf',
            'caption' => 'Nice Caption',
        ]);
        $fileWithoutCaption = File::fromArray([
            'uuid' => 'test-2',
            'filename' => 'file.pdf',
        ]);

        $this->assertSame('Nice Caption', $fileWithCaption->getDisplayName());
        $this->assertSame('file.pdf', $fileWithoutCaption->getDisplayName());
    }

    public function testToArray(): void
    {
        $data = ['uuid' => 'test-789', 'filename' => 'test.pdf'];
        $file = File::fromArray($data);

        $this->assertSame($data, $file->toArray());
    }
}
