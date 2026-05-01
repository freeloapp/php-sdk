<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * ProjectFull model.
 */
class ProjectFull
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $name = null,
        public readonly ?\DateTimeImmutable $dateAdd = null,
        public readonly ?\DateTimeImmutable $dateEditedAt = null,
        public readonly mixed $owner,
        public readonly mixed $state,
        public readonly ?int $minutesBudget = null,
        public readonly mixed $budget,
        public readonly ?int $realMinutesSpent = null,
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
            dateAdd: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['date_add'] ?? null),
            dateEditedAt: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['date_edited_at'] ?? null),
            owner: isset($data['owner']) ? $data['owner'] : null,
            state: isset($data['state']) ? $data['state'] : null,
            minutesBudget: isset($data['minutes_budget']) ? (int) $data['minutes_budget'] : null,
            budget: isset($data['budget']) ? $data['budget'] : null,
            realMinutesSpent: isset($data['real_minutes_spent']) ? (int) $data['real_minutes_spent'] : null,
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
