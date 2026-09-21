<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * Origin task this task was copied from (template copy or multi-project copy); `null` otherwise.
 *
 * CopiedFromTask model.
 */
class CopiedFromTask
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly array $project = [],
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
            project: isset($data['project']) && is_array($data['project'])
                ? $data['project'] : [],
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
