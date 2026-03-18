<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

class TaskWork
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $reported = null,
        public readonly ?int $minutes = null,
        public readonly mixed $cost,
        public readonly ?string $notice = null,
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
            reported: isset($data['reported']) ? (string) $data['reported'] : null,
            minutes: isset($data['minutes']) ? (int) $data['minutes'] : null,
            cost: isset($data['cost']) ? $data['cost'] : null,
            notice: isset($data['notice']) ? (string) $data['notice'] : null,
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
