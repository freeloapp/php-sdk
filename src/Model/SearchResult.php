<?php

declare(strict_types=1);

namespace Freelo\Sdk\Model;

/**
 * Represents a search result item
 */
class SearchResult
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        public readonly int $id,
        public readonly string $type,
        public readonly ?string $name = null,
        public readonly ?string $content = null,
        public readonly ?int $projectId = null,
        public readonly ?string $projectName = null,
        public readonly array $data = [],
    ) {
    }

    /**
     * Create a SearchResult from API response data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            type: isset($data['type']) ? (string) $data['type'] : '',
            name: isset($data['name']) ? (string) $data['name'] : null,
            content: isset($data['content']) ? (string) $data['content'] : null,
            projectId: isset($data['project_id']) ? (int) $data['project_id'] : null,
            projectName: isset($data['project_name']) ? (string) $data['project_name'] : null,
            data: $data,
        );
    }

    /**
     * Check if this is a task result
     */
    public function isTask(): bool
    {
        return $this->type === 'task';
    }

    /**
     * Check if this is a project result
     */
    public function isProject(): bool
    {
        return $this->type === 'project';
    }

    /**
     * Check if this is a comment result
     */
    public function isComment(): bool
    {
        return $this->type === 'comment';
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
