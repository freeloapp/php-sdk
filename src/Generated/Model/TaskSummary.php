<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * TaskSummary model.
 */
class TaskSummary
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $name = null,
        public readonly ?\DateTimeImmutable $dateAdd = null,
        public readonly ?\DateTimeImmutable $dateEditedAt = null,
        public readonly ?\DateTimeImmutable $dueDate = null,
        public readonly ?\DateTimeImmutable $dueDateEnd = null,
        public readonly ?int $countComments = null,
        public readonly ?int $countSubtasks = null,
        public readonly mixed $author,
        public readonly mixed $worker,
        public readonly array $labels = [],
        public readonly ?int $parentTaskId = null,
        public readonly mixed $totalTimeEstimate,
        public readonly array $usersTimeEstimates = [],
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
            dueDate: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['due_date'] ?? null),
            dueDateEnd: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['due_date_end'] ?? null),
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
