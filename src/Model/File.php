<?php

declare(strict_types=1);

namespace Freelo\Sdk\Model;

use Freelo\Sdk\Internal\DateTimeParser;

/**
 * Represents a Freelo file (supports both FileBasic, FileFull, and FileItem schemas)
 */
class File
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        public readonly string $uuid,
        public readonly string $filename,
        public readonly ?int $id = null,
        public readonly ?int $size = null,
        public readonly ?string $caption = null,
        public readonly ?string $description = null,
        public readonly ?\DateTimeImmutable $dateAdd = null,
        public readonly ?\DateTimeImmutable $dateEditedAt = null,
        public readonly ?State $state = null,
        public readonly ?string $mimeType = null,
        public readonly ?User $author = null,
        public readonly ?Project $project = null,
        public readonly ?string $directoryUuid = null,
        public readonly ?int $order = null,
        public readonly ?string $type = null,
        public readonly ?string $extension = null,
        public readonly ?string $color = null,
        public readonly ?int $itemsCount = null,
        public readonly ?string $link = null,
        public readonly ?string $linkType = null,
        public readonly ?string $note = null,
        public readonly array $data = [],
    ) {
    }

    /**
     * Create a File from API response data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $filename = $data['filename'] ?? $data['name'] ?? '';

        return new self(
            uuid: (string) ($data['uuid'] ?? ''),
            filename: (string) $filename,
            id: isset($data['id']) ? (int) $data['id'] : null,
            size: isset($data['size']) ? (int) $data['size'] : null,
            caption: isset($data['caption']) ? (string) $data['caption'] : null,
            description: isset($data['description']) ? (string) $data['description'] : null,
            dateAdd: DateTimeParser::parseDateTime($data['date_add'] ?? null),
            dateEditedAt: DateTimeParser::parseDateTime($data['date_edited_at'] ?? null),
            state: isset($data['state']) && is_array($data['state']) ? State::fromArray($data['state']) : null,
            mimeType: isset($data['mime_type']) ? (string) $data['mime_type'] : null,
            author: isset($data['author']) && is_array($data['author']) ? User::fromArray($data['author']) : null,
            project: isset($data['project']) && is_array($data['project'])
                ? Project::fromArray($data['project']) : null,
            directoryUuid: isset($data['directory_uuid']) ? (string) $data['directory_uuid'] : null,
            order: isset($data['order']) ? (int) $data['order'] : null,
            type: isset($data['type']) ? (string) $data['type'] : null,
            extension: isset($data['extension']) ? (string) $data['extension'] : null,
            color: isset($data['color']) ? (string) $data['color'] : null,
            itemsCount: isset($data['items_count']) ? (int) $data['items_count'] : null,
            link: isset($data['link']) ? (string) $data['link'] : null,
            linkType: isset($data['link_type']) ? (string) $data['link_type'] : null,
            note: isset($data['note']) ? (string) $data['note'] : null,
            data: $data,
        );
    }

    /**
     * Get display name (caption if available, otherwise filename)
     */
    public function getDisplayName(): string
    {
        return $this->caption ?? $this->filename;
    }

    /**
     * Check if this is a directory
     */
    public function isDirectory(): bool
    {
        return $this->type === 'directory';
    }

    /**
     * Check if this is a link
     */
    public function isLink(): bool
    {
        return $this->type === 'link';
    }

    /**
     * Check if this is a document
     */
    public function isDocument(): bool
    {
        return $this->type === 'document';
    }

    /**
     * Check if this is a file
     */
    public function isFile(): bool
    {
        return $this->type === 'file';
    }

    /**
     * Convert to array
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
