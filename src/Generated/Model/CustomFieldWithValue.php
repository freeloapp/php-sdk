<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * CustomFieldWithValue model.
 */
class CustomFieldWithValue
{
    public function __construct(
        public readonly ?string $fieldUuid = null,
        public readonly ?string $customFieldsTypesUuid = null,
        public readonly ?int $projectId = null,
        public readonly ?string $name = null,
        public readonly ?int $priority = null,
        public readonly ?string $fieldDateAdd = null,
        public readonly ?string $valueUuid = null,
        public readonly ?int $valueAuthorId = null,
        public readonly ?string $value = null,
        public readonly ?string $valueDateAdd = null,
        public readonly ?string $valueDateEditedAt = null,
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
            fieldUuid: isset($data['field_uuid']) ? (string) $data['field_uuid'] : null,
            customFieldsTypesUuid: isset($data['custom_fields_types_uuid']) ? (string) $data['custom_fields_types_uuid'] : null,
            projectId: isset($data['project_id']) ? (int) $data['project_id'] : null,
            name: isset($data['name']) ? (string) $data['name'] : null,
            priority: isset($data['priority']) ? (int) $data['priority'] : null,
            fieldDateAdd: isset($data['field_date_add']) ? (string) $data['field_date_add'] : null,
            valueUuid: isset($data['value_uuid']) ? (string) $data['value_uuid'] : null,
            valueAuthorId: isset($data['value_author_id']) ? (int) $data['value_author_id'] : null,
            value: isset($data['value']) ? (string) $data['value'] : null,
            valueDateAdd: isset($data['value_date_add']) ? (string) $data['value_date_add'] : null,
            valueDateEditedAt: isset($data['value_date_edited_at']) ? (string) $data['value_date_edited_at'] : null,
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
