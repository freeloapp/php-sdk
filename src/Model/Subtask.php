<?php

declare(strict_types=1);

namespace Freelo\Sdk\Model;

/**
 * Represents a task subtask
 */
class Subtask
{
    /**
     * @param TaskLabel[] $labels
     * @param array<string, mixed> $data
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?int $taskId = null,
        public readonly ?string $dateAdd = null,
        public readonly ?string $dateEditedAt = null,
        public readonly ?string $dueDate = null,
        public readonly ?string $dueDateEnd = null,
        public readonly ?string $priorityEnum = null,
        public readonly ?int $countComments = null,
        public readonly ?int $countSubtasks = null,
        public readonly ?User $author = null,
        public readonly ?User $worker = null,
        public readonly ?State $state = null,
        public readonly ?Project $project = null,
        public readonly ?Tasklist $tasklist = null,
        public readonly array $labels = [],
        public readonly array $data = [],
    ) {
    }

    /**
     * Create a Subtask from API response data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $labelsData = isset($data['labels']) && is_array($data['labels']) ? $data['labels'] : [];

        return new self(
            id: (int) ($data['id'] ?? 0),
            name: isset($data['name']) ? (string) $data['name'] : '',
            taskId: isset($data['task_id']) ? (int) $data['task_id'] : null,
            dateAdd: isset($data['date_add']) ? (string) $data['date_add'] : null,
            dateEditedAt: isset($data['date_edited_at']) ? (string) $data['date_edited_at'] : null,
            dueDate: isset($data['due_date']) ? (string) $data['due_date'] : null,
            dueDateEnd: isset($data['due_date_end']) ? (string) $data['due_date_end'] : null,
            priorityEnum: isset($data['priority_enum']) ? (string) $data['priority_enum'] : null,
            countComments: isset($data['count_comments']) ? (int) $data['count_comments'] : null,
            countSubtasks: isset($data['count_subtasks']) ? (int) $data['count_subtasks'] : null,
            author: isset($data['author']) && is_array($data['author']) ? User::fromArray($data['author']) : null,
            worker: isset($data['worker']) && is_array($data['worker']) ? User::fromArray($data['worker']) : null,
            state: isset($data['state']) && is_array($data['state']) ? State::fromArray($data['state']) : null,
            project: isset($data['project']) && is_array($data['project']) ? Project::fromArray($data['project']) : null,
            tasklist: isset($data['tasklist']) && is_array($data['tasklist']) ? Tasklist::fromArray($data['tasklist']) : null,
            labels: array_map(
                fn (array $l) => TaskLabel::fromArray($l),
                $labelsData
            ),
            data: $data,
        );
    }

    /**
     * Convert to array
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
