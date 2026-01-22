<?php

declare(strict_types=1);

namespace Freelo\Sdk\Model;

/**
 * Represents a pinned item (link or entity pinned to a project)
 */
class PinnedItem
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        public readonly int $id,
        public readonly string $type,
        public readonly ?string $name = null,
        public readonly ?int $entityId = null,
        public readonly ?string $link = null,
        public readonly ?string $title = null,
        public readonly array $data = [],
    ) {
    }

    /**
     * Create a PinnedItem from API response data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            type: isset($data['type']) ? (string) $data['type'] : '',
            name: isset($data['name']) ? (string) $data['name'] : null,
            entityId: isset($data['entity_id']) ? (int) $data['entity_id'] : null,
            link: isset($data['link']) ? (string) $data['link'] : null,
            title: isset($data['title']) ? (string) $data['title'] : null,
            data: $data,
        );
    }

    /**
     * Check if this pinned item is a task
     */
    public function isTask(): bool
    {
        return $this->type === 'task';
    }

    /**
     * Check if this pinned item is a project
     */
    public function isProject(): bool
    {
        return $this->type === 'project';
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
