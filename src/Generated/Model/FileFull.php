<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * FileFull model.
 */
class FileFull
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $uuid = null,
        public readonly ?string $filename = null,
        public readonly ?int $size = null,
        public readonly ?string $caption = null,
        public readonly ?string $description = null,
        public readonly ?\DateTimeImmutable $dateAdd = null,
        public readonly ?\DateTimeImmutable $dateEditedAt = null,
        public readonly mixed $state,
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
            filename: isset($data['filename']) ? (string) $data['filename'] : null,
            size: isset($data['size']) ? (int) $data['size'] : null,
            caption: isset($data['caption']) ? (string) $data['caption'] : null,
            description: isset($data['description']) ? (string) $data['description'] : null,
            dateAdd: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['date_add'] ?? null),
            dateEditedAt: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['date_edited_at'] ?? null),
            state: isset($data['state']) ? $data['state'] : null,
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
