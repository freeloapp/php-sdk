<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * TaskLabelColor model.
 */
class TaskLabelColor
{
    public function __construct(
        public readonly ?string $color = null,
        public readonly ?string $displayName = null,
        public readonly ?bool $isDefault = null,
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
            color: isset($data['color']) ? (string) $data['color'] : null,
            displayName: isset($data['display_name']) ? (string) $data['display_name'] : null,
            isDefault: isset($data['is_default']) ? (bool) $data['is_default'] : null,
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
