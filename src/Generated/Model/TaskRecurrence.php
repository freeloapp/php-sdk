<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * Current recurrence of the task's series. Same keys as `TaskRecurrenceInput`, only those of its `type`,
 * plus the read-only `state`.
 *
 * TaskRecurrence model.
 */
class TaskRecurrence
{
    public function __construct(
        public readonly string $type,
        public readonly \DateTimeImmutable $dateStart,
        public readonly ?string $endType = null,
        public readonly ?int $endRecurrenceCount = null,
        public readonly ?\DateTimeImmutable $dateEnd = null,
        public readonly ?bool $isDueDateEnabled = null,
        public readonly ?int $dueDatePlusDays = null,
        public readonly ?string $dueDateTime = null,
        public readonly ?bool $onlyReactivateTask = null,
        public readonly ?bool $copyFirstComment = null,
        public readonly ?bool $keepTasklist = null,
        public readonly ?bool $keepExistingTaskcheckStates = null,
        public readonly ?bool $keepCustomFieldsValues = null,
        public readonly ?int $everyNthDay = null,
        public readonly ?bool $onlyWorkDays = null,
        public readonly ?int $everyNthWeek = null,
        public readonly array $daysOfWeek = [],
        public readonly ?int $everyNthMonth = null,
        public readonly ?string $monthlyYearlyType = null,
        public readonly ?int $dayOfMonth = null,
        public readonly ?string $nthOccurence = null,
        public readonly mixed $dayOfWeek,
        public readonly ?int $everyNthYear = null,
        public readonly array $monthsOfYear = [],
        public readonly ?string $state = null,
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
            type: (string) ($data['type'] ?? ''),
            dateStart: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['date_start'] ?? null),
            endType: isset($data['end_type']) ? (string) $data['end_type'] : null,
            endRecurrenceCount: isset($data['end_recurrence_count']) ? (int) $data['end_recurrence_count'] : null,
            dateEnd: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['date_end'] ?? null),
            isDueDateEnabled: isset($data['is_due_date_enabled']) ? (bool) $data['is_due_date_enabled'] : null,
            dueDatePlusDays: isset($data['due_date_plus_days']) ? (int) $data['due_date_plus_days'] : null,
            dueDateTime: isset($data['due_date_time']) ? (string) $data['due_date_time'] : null,
            onlyReactivateTask: isset($data['only_reactivate_task']) ? (bool) $data['only_reactivate_task'] : null,
            copyFirstComment: isset($data['copy_first_comment']) ? (bool) $data['copy_first_comment'] : null,
            keepTasklist: isset($data['keep_tasklist']) ? (bool) $data['keep_tasklist'] : null,
            keepExistingTaskcheckStates: isset($data['keep_existing_taskcheck_states']) ? (bool) $data['keep_existing_taskcheck_states'] : null,
            keepCustomFieldsValues: isset($data['keep_custom_fields_values']) ? (bool) $data['keep_custom_fields_values'] : null,
            everyNthDay: isset($data['every_nth_day']) ? (int) $data['every_nth_day'] : null,
            onlyWorkDays: isset($data['only_work_days']) ? (bool) $data['only_work_days'] : null,
            everyNthWeek: isset($data['every_nth_week']) ? (int) $data['every_nth_week'] : null,
            daysOfWeek: isset($data['days_of_week']) && is_array($data['days_of_week'])
                ? $data['days_of_week'] : [],
            everyNthMonth: isset($data['every_nth_month']) ? (int) $data['every_nth_month'] : null,
            monthlyYearlyType: isset($data['monthly_yearly_type']) ? (string) $data['monthly_yearly_type'] : null,
            dayOfMonth: isset($data['day_of_month']) ? (int) $data['day_of_month'] : null,
            nthOccurence: isset($data['nth_occurence']) ? (string) $data['nth_occurence'] : null,
            dayOfWeek: isset($data['day_of_week']) ? $data['day_of_week'] : null,
            everyNthYear: isset($data['every_nth_year']) ? (int) $data['every_nth_year'] : null,
            monthsOfYear: isset($data['months_of_year']) && is_array($data['months_of_year'])
                ? $data['months_of_year'] : [],
            state: isset($data['state']) ? (string) $data['state'] : null,
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
