<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

class CustomFieldEnumValue
{
    public function __construct(
        public readonly ?string $uuid = null,
        public readonly ?int $taskId = null,
        public readonly ?string $customFieldUuid = null,
        public readonly ?string $value = null,
        public readonly ?string $dateAdd = null,
        public readonly ?string $dateEditedAt = null,
        public readonly ?int $authorId = null,
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
            uuid: isset($data['uuid']) ? (string) $data['uuid'] : null,
            taskId: isset($data['task_id']) ? (int) $data['task_id'] : null,
            customFieldUuid: isset($data['custom_field_uuid']) ? (string) $data['custom_field_uuid'] : null,
            value: isset($data['value']) ? (string) $data['value'] : null,
            dateAdd: isset($data['date_add']) ? (string) $data['date_add'] : null,
            dateEditedAt: isset($data['date_edited_at']) ? (string) $data['date_edited_at'] : null,
            authorId: isset($data['author_id']) ? (int) $data['author_id'] : null,
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
