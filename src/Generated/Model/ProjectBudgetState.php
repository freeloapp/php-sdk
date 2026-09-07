<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * Current budget state — configured settings plus consumption (čerpání) and remaining (zbývá)
 * values for both money and time. All monetary amounts use the `Currency` format (amount ×100).
 *
 * ProjectBudgetState model.
 */
class ProjectBudgetState
{
    public function __construct(
        public readonly bool $isRecurrent,
        public readonly int $nullInDayOfMonth,
        public readonly int $nullAfterMonthsCount,
        public readonly mixed $budget,
        public readonly int $minutesBudget,
        public readonly mixed $spentCost,
        public readonly int $spentMinutes,
        public readonly mixed $remainingCost,
        public readonly int $remainingMinutes,
        public readonly ?\DateTimeImmutable $nextResetDate,
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
            isRecurrent: (bool) ($data['is_recurrent'] ?? false),
            nullInDayOfMonth: (int) ($data['null_in_day_of_month'] ?? 0),
            nullAfterMonthsCount: (int) ($data['null_after_months_count'] ?? 0),
            budget: $data['budget'] ?? null,
            minutesBudget: (int) ($data['minutes_budget'] ?? 0),
            spentCost: $data['spent_cost'] ?? null,
            spentMinutes: (int) ($data['spent_minutes'] ?? 0),
            remainingCost: $data['remaining_cost'] ?? null,
            remainingMinutes: (int) ($data['remaining_minutes'] ?? 0),
            nextResetDate: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['next_reset_date'] ?? null),
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
