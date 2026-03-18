<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

class Comment
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $content = null,
        public readonly ?string $dateAdd = null,
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
            content: isset($data['content']) ? (string) $data['content'] : null,
            dateAdd: isset($data['date_add']) ? (string) $data['date_add'] : null,
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
