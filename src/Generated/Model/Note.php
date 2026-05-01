<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * Note model.
 */
class Note
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $name = null,
        public readonly ?\DateTimeImmutable $dateAdd = null,
        public readonly ?\DateTimeImmutable $dateEditedAt = null,
        public readonly mixed $state,
        public readonly ?string $content = null,
        public readonly mixed $author,
        public readonly mixed $project,
        public readonly array $files = [],
        public readonly array $comments = [],
        /** @var array<string, mixed> */
        public readonly array $data = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: isset($data['id']) ? (int) $data['id'] : null,
            name: isset($data['name']) ? (string) $data['name'] : null,
            dateAdd: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['date_add'] ?? null),
            dateEditedAt: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['date_edited_at'] ?? null),
            state: isset($data['state']) ? $data['state'] : null,
            content: isset($data['content']) ? (string) $data['content'] : null,
            author: isset($data['author']) ? $data['author'] : null,
            project: isset($data['project']) ? $data['project'] : null,
            files: isset($data['files']) && is_array($data['files'])
                ? $data['files'] : [],
            comments: isset($data['comments']) && is_array($data['comments'])
                ? $data['comments'] : [],
            data: $data,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
