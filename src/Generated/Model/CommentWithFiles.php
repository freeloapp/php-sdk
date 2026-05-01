<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * CommentWithFiles model.
 */
class CommentWithFiles
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $content = null,
        public readonly ?\DateTimeImmutable $dateAdd = null,
        public readonly array $files = [],
        public readonly mixed $author,
        public readonly ?bool $isDescription = null,
        public readonly array $commentsReactions = [],
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
            content: isset($data['content']) ? (string) $data['content'] : null,
            dateAdd: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['date_add'] ?? null),
            files: isset($data['files']) && is_array($data['files'])
                ? $data['files'] : [],
            author: isset($data['author']) ? $data['author'] : null,
            isDescription: isset($data['is_description']) ? (bool) $data['is_description'] : null,
            commentsReactions: isset($data['comments_reactions']) && is_array($data['comments_reactions'])
                ? $data['comments_reactions'] : [],
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
