<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * TaskRelation model.
 */
class TaskRelation
{
    public function __construct(
        public readonly ?string $type = null,
        public readonly ?int $relatedTaskId = null,
        public readonly ?string $relatedTaskName = null,
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
            type: isset($data['type']) ? (string) $data['type'] : null,
            relatedTaskId: isset($data['related_task_id']) ? (int) $data['related_task_id'] : null,
            relatedTaskName: isset($data['related_task_name']) ? (string) $data['related_task_name'] : null,
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
