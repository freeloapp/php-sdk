<?php

declare(strict_types=1);

namespace Freelo\Sdk\Model;

/**
 * Represents a Freelo comment
 */
class Comment
{
    /**
     * @param File[] $files
     * @param User[] $commentsReactions
     * @param array<string, mixed>|null $task
     * @param array<string, mixed>|null $document
     * @param array<string, mixed>|null $link
     * @param array<string, mixed>|null $file
     * @param array<string, mixed> $data
     */
    public function __construct(
        public readonly int $id,
        public readonly ?string $uuid = null,
        public readonly ?string $content = null,
        public readonly ?string $dateAdd = null,
        public readonly ?string $dateEditedAt = null,
        public readonly ?User $author = null,
        public readonly ?array $task = null,
        public readonly ?Tasklist $tasklist = null,
        public readonly ?Project $project = null,
        public readonly ?array $document = null,
        public readonly ?array $link = null,
        public readonly ?array $file = null,
        public readonly ?bool $isDescription = null,
        public readonly array $files = [],
        public readonly array $commentsReactions = [],
        public readonly array $data = [],
    ) {
    }

    /**
     * Create a Comment from API response data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $content = $data['content'] ?? $data['text'] ?? null;
        $filesData = isset($data['files']) && is_array($data['files']) ? $data['files'] : [];
        $reactionsData = isset($data['comments_reactions']) && is_array($data['comments_reactions']) ? $data['comments_reactions'] : [];

        return new self(
            id: (int) ($data['id'] ?? 0),
            uuid: isset($data['uuid']) ? (string) $data['uuid'] : null,
            content: $content !== null ? (string) $content : null,
            dateAdd: isset($data['date_add']) ? (string) $data['date_add'] : null,
            dateEditedAt: isset($data['date_edited_at']) ? (string) $data['date_edited_at'] : null,
            author: isset($data['author']) && is_array($data['author']) ? User::fromArray($data['author']) : null,
            task: isset($data['task']) && is_array($data['task']) ? $data['task'] : null,
            tasklist: isset($data['tasklist']) && is_array($data['tasklist']) ? Tasklist::fromArray($data['tasklist']) : null,
            project: isset($data['project']) && is_array($data['project']) ? Project::fromArray($data['project']) : null,
            document: isset($data['document']) && is_array($data['document']) ? $data['document'] : null,
            link: isset($data['link']) && is_array($data['link']) ? $data['link'] : null,
            file: isset($data['file']) && is_array($data['file']) ? $data['file'] : null,
            isDescription: isset($data['is_description']) ? (bool) $data['is_description'] : null,
            files: array_map(
                fn (array $f) => File::fromArray($f),
                $filesData
            ),
            commentsReactions: array_map(
                fn (array $u) => User::fromArray($u),
                $reactionsData
            ),
            data: $data,
        );
    }

    /**
     * Get task ID from task object if available
     */
    public function getTaskId(): ?int
    {
        return isset($this->task['id']) ? (int) $this->task['id'] : null;
    }

    /**
     * Get task name from task object if available
     */
    public function getTaskName(): ?string
    {
        return isset($this->task['name']) ? (string) $this->task['name'] : null;
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
