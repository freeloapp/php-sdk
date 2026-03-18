<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

class TasklistFull
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $name = null,
        public readonly ?string $dateAdd = null,
        public readonly ?string $dateEditedAt = null,
        public readonly mixed $state,
        public readonly mixed $project,
        public readonly ?int $realMinutesSpent = null,
        public readonly mixed $budget,
        public readonly mixed $realCost,
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
            name: isset($data['name']) ? (string) $data['name'] : null,
            dateAdd: isset($data['date_add']) ? (string) $data['date_add'] : null,
            dateEditedAt: isset($data['date_edited_at']) ? (string) $data['date_edited_at'] : null,
            state: isset($data['state']) ? $data['state'] : null,
            project: isset($data['project']) ? $data['project'] : null,
            realMinutesSpent: isset($data['real_minutes_spent']) ? (int) $data['real_minutes_spent'] : null,
            budget: isset($data['budget']) ? $data['budget'] : null,
            realCost: isset($data['real_cost']) ? $data['real_cost'] : null,
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
