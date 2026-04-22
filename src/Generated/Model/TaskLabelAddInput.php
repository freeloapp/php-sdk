<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * Two input modes: (1) UUID only — reference an existing label by UUID, assigned as-is. (2) Name-based — name is required; color defaults to #77787a if omitted; uuid is auto-generated if omitted. Matching is by name+color — if an existing label matches both, it is reused; otherwise a new label is created.
 *
 * TaskLabelAddInput model.
 */
class TaskLabelAddInput
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
