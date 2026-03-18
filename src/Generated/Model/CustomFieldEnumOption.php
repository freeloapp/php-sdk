<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

class CustomFieldEnumOption
{
    public function __construct(
        public readonly ?string $enumUuid = null,
        public readonly ?string $enumValue = null,
        public readonly ?string $customFieldUuid = null,
        public readonly ?string $customFieldName = null,
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
            enumUuid: isset($data['enum_uuid']) ? (string) $data['enum_uuid'] : null,
            enumValue: isset($data['enum_value']) ? (string) $data['enum_value'] : null,
            customFieldUuid: isset($data['custom_field_uuid']) ? (string) $data['custom_field_uuid'] : null,
            customFieldName: isset($data['custom_field_name']) ? (string) $data['custom_field_name'] : null,
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
