<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * ClientProjects model.
 */
class ClientProjects
{
    public function __construct(
        public readonly array $projects = [],
        public readonly ?int $totalCount = null,
        public readonly ?int $visibleCount = null,
        public readonly ?bool $isComplete = null,
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
            projects: isset($data['projects']) && is_array($data['projects'])
                ? $data['projects'] : [],
            totalCount: isset($data['total_count']) ? (int) $data['total_count'] : null,
            visibleCount: isset($data['visible_count']) ? (int) $data['visible_count'] : null,
            isComplete: isset($data['is_complete']) ? (bool) $data['is_complete'] : null,
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
