<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * Three input modes: (1) UUID — removes the label identified by UUID. (2) Name only — removes all labels with that name regardless of color. (3) Name + color — removes the label matching both name and color.
 *
 * TaskLabelRemoveInput model.
 */
class TaskLabelRemoveInput
{
    public function __construct(
        public readonly ?string $uuid = null,
        public readonly ?string $name = null,
        public readonly ?string $color = null,
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
            name: isset($data['name']) ? (string) $data['name'] : null,
            color: isset($data['color']) ? (string) $data['color'] : null,
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
