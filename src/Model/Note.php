<?php

declare(strict_types=1);

namespace Freelo\Sdk\Model;

use Freelo\Sdk\Internal\DateTimeParser;

/**
 * Represents a project note
 */
class Note
{
    /**
     * @param File[] $files
     * @param Comment[] $comments
     * @param array<string, mixed> $data
     */
    public function __construct(
        public readonly int $id,
        public readonly ?string $name = null,
        public readonly ?string $content = null,
        public readonly ?\DateTimeImmutable $dateAdd = null,
        public readonly ?\DateTimeImmutable $dateEditedAt = null,
        public readonly ?State $state = null,
        public readonly ?User $author = null,
        public readonly ?Project $project = null,
        public readonly ?int $projectId = null,
        public readonly array $files = [],
        public readonly array $comments = [],
        public readonly array $data = [],
    ) {
    }

    /**
     * Create a Note from API response data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $filesData = isset($data['files']) && is_array($data['files']) ? $data['files'] : [];
        $commentsData = isset($data['comments']) && is_array($data['comments']) ? $data['comments'] : [];

        return new self(
            id: (int) ($data['id'] ?? 0),
            name: isset($data['name']) ? (string) $data['name'] : null,
            content: isset($data['content']) ? (string) $data['content'] : null,
            dateAdd: DateTimeParser::parseDateTime($data['date_add'] ?? null),
            dateEditedAt: DateTimeParser::parseDateTime($data['date_edited_at'] ?? null),
            state: isset($data['state']) && is_array($data['state']) ? State::fromArray($data['state']) : null,
            author: isset($data['author']) && is_array($data['author']) ? User::fromArray($data['author']) : null,
            project: isset($data['project']) && is_array($data['project'])
                ? Project::fromArray($data['project']) : null,
            projectId: isset($data['project_id']) ? (int) $data['project_id'] : null,
            files: array_map(
                fn (array $f) => File::fromArray($f),
                $filesData
            ),
            comments: array_map(
                fn (array $c) => Comment::fromArray($c),
                $commentsData
            ),
            data: $data,
        );
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
