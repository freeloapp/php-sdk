<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * CustomFieldEnumValue model.
 */
class CustomFieldEnumValue
{
    public function __construct(
        public readonly ?string $uuid = null,
        public readonly ?int $taskId = null,
        public readonly ?string $customFieldUuid = null,
        public readonly ?string $value = null,
        public readonly ?\DateTimeImmutable $dateAdd = null,
        public readonly ?\DateTimeImmutable $dateEditedAt = null,
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
            dateAdd: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['date_add'] ?? null),
            dateEditedAt: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['date_edited_at'] ?? null),
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
