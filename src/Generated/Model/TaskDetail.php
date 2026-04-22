<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * TaskDetail model.
 */
class TaskDetail
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $name = null,
        public readonly ?string $dateAdd = null,
        public readonly ?string $dateEditedAt = null,
        public readonly ?string $dueDate = null,
        public readonly ?string $dueDateEnd = null,
        public readonly ?string $dateFinished = null,
        public readonly ?int $minutes = null,
        public readonly ?string $priorityEnum = null,
        public readonly ?int $countSubtasks = null,
        public readonly ?int $parentTaskId = null,
        public readonly mixed $cost,
        public readonly mixed $author,
        public readonly mixed $worker,
        public readonly mixed $state,
        public readonly array $comments = [],
        public readonly array $labels = [],
        public readonly mixed $project,
        public readonly mixed $tasklist,
        public readonly array $customFields = [],
        public readonly mixed $totalTimeEstimate,
        public readonly array $usersTimeEstimates = [],
        public readonly array $trackingUsers = [],
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
            dateFinished: isset($data['date_finished']) ? (string) $data['date_finished'] : null,
            minutes: isset($data['minutes']) ? (int) $data['minutes'] : null,
            priorityEnum: isset($data['priority_enum']) ? (string) $data['priority_enum'] : null,
            countSubtasks: isset($data['count_subtasks']) ? (int) $data['count_subtasks'] : null,
            parentTaskId: isset($data['parent_task_id']) ? (int) $data['parent_task_id'] : null,
            cost: isset($data['cost']) ? $data['cost'] : null,
            author: isset($data['author']) ? $data['author'] : null,
            worker: isset($data['worker']) ? $data['worker'] : null,
            state: isset($data['state']) ? $data['state'] : null,
            comments: isset($data['comments']) && is_array($data['comments'])
                ? $data['comments'] : [],
            labels: isset($data['labels']) && is_array($data['labels'])
                ? $data['labels'] : [],
            project: isset($data['project']) ? $data['project'] : null,
            tasklist: isset($data['tasklist']) ? $data['tasklist'] : null,
            customFields: isset($data['custom_fields']) && is_array($data['custom_fields'])
                ? $data['custom_fields'] : [],
            totalTimeEstimate: isset($data['total_time_estimate']) ? $data['total_time_estimate'] : null,
            usersTimeEstimates: isset($data['users_time_estimates']) && is_array($data['users_time_estimates'])
                ? $data['users_time_estimates'] : [],
            trackingUsers: isset($data['tracking_users']) && is_array($data['tracking_users'])
                ? $data['tracking_users'] : [],
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
