<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

class CommentFull
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $uuid = null,
        public readonly ?string $content = null,
        public readonly ?string $dateAdd = null,
        public readonly ?string $dateEditedAt = null,
        public readonly mixed $author,
        public readonly array $task = [],
        public readonly mixed $tasklist,
        public readonly mixed $project,
        public readonly array $document = [],
        public readonly array $link = [],
        public readonly array $file = [],
        public readonly array $files = [],
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
            uuid: isset($data['uuid']) ? (string) $data['uuid'] : null,
            content: isset($data['content']) ? (string) $data['content'] : null,
            dateAdd: isset($data['date_add']) ? (string) $data['date_add'] : null,
            dateEditedAt: isset($data['date_edited_at']) ? (string) $data['date_edited_at'] : null,
            author: isset($data['author']) ? $data['author'] : null,
            task: isset($data['task']) && is_array($data['task'])
                ? $data['task'] : [],
            tasklist: isset($data['tasklist']) ? $data['tasklist'] : null,
            project: isset($data['project']) ? $data['project'] : null,
            document: isset($data['document']) && is_array($data['document'])
                ? $data['document'] : [],
            link: isset($data['link']) && is_array($data['link'])
                ? $data['link'] : [],
            file: isset($data['file']) && is_array($data['file'])
                ? $data['file'] : [],
            files: isset($data['files']) && is_array($data['files'])
                ? $data['files'] : [],
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
