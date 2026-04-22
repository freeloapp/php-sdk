<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * TaskFull model.
 */
class TaskFull
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $name = null,
        public readonly ?string $dateAdd = null,
        public readonly ?string $dateEditedAt = null,
        public readonly ?string $dueDate = null,
        public readonly ?string $dueDateEnd = null,
        public readonly ?int $countComments = null,
        public readonly ?int $countSubtasks = null,
        public readonly mixed $author,
        public readonly mixed $worker,
        public readonly array $labels = [],
        public readonly ?int $parentTaskId = null,
        public readonly mixed $totalTimeEstimate,
        public readonly array $usersTimeEstimates = [],
        public readonly mixed $state,
        public readonly mixed $project,
        public readonly mixed $tasklist,
        public readonly array $customFields = [],
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
            dueDate: isset($data['due_date']) ? (string) $data['due_date'] : null,
            dueDateEnd: isset($data['due_date_end']) ? (string) $data['due_date_end'] : null,
            countComments: isset($data['count_comments']) ? (int) $data['count_comments'] : null,
            countSubtasks: isset($data['count_subtasks']) ? (int) $data['count_subtasks'] : null,
            author: isset($data['author']) ? $data['author'] : null,
            worker: isset($data['worker']) ? $data['worker'] : null,
            labels: isset($data['labels']) && is_array($data['labels'])
                ? $data['labels'] : [],
            parentTaskId: isset($data['parent_task_id']) ? (int) $data['parent_task_id'] : null,
            totalTimeEstimate: isset($data['total_time_estimate']) ? $data['total_time_estimate'] : null,
            usersTimeEstimates: isset($data['users_time_estimates']) && is_array($data['users_time_estimates'])
                ? $data['users_time_estimates'] : [],
            state: isset($data['state']) ? $data['state'] : null,
            project: isset($data['project']) ? $data['project'] : null,
            tasklist: isset($data['tasklist']) ? $data['tasklist'] : null,
            customFields: isset($data['custom_fields']) && is_array($data['custom_fields'])
                ? $data['custom_fields'] : [],
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
