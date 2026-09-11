<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * Budget settings to set or change.
 *
 * To **cancel** the budget, send `budget: null` and `minutes_budget: 0` (with `is_recurrent: false`)
 * — it resets to empty/zero. The budget currency always follows the project currency.
 *
 * ProjectBudgetSettingsInput model.
 */
class ProjectBudgetSettingsInput
{
    public function __construct(
        public readonly bool $isRecurrent,
        public readonly ?int $nullInDayOfMonth = null,
        public readonly ?int $nullAfterMonthsCount = null,
        public readonly ?string $budget = null,
        public readonly ?int $minutesBudget = null,
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
            nullInDayOfMonth: isset($data['null_in_day_of_month']) ? (int) $data['null_in_day_of_month'] : null,
            nullAfterMonthsCount: isset($data['null_after_months_count']) ? (int) $data['null_after_months_count'] : null,
            budget: isset($data['budget']) ? (string) $data['budget'] : null,
            minutesBudget: isset($data['minutes_budget']) ? (int) $data['minutes_budget'] : null,
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
