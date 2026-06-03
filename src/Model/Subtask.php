<?php

declare(strict_types=1);

namespace Freelo\Sdk\Model;

use Freelo\Sdk\Internal\DateTimeParser;

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
        public readonly ?string $type = null,
        public readonly ?int $taskId = null,
        public readonly ?\DateTimeImmutable $dateAdd = null,
        public readonly ?\DateTimeImmutable $dateEditedAt = null,
        public readonly ?\DateTimeImmutable $dueDate = null,
        public readonly ?\DateTimeImmutable $dueDateEnd = null,
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
            type: isset($data['type']) ? (string) $data['type'] : null,
            taskId: isset($data['task_id']) ? (int) $data['task_id'] : null,
            dateAdd: DateTimeParser::parseDateTime($data['date_add'] ?? null),
            dateEditedAt: DateTimeParser::parseDateTime($data['date_edited_at'] ?? null),
            dueDate: DateTimeParser::parseDateTime($data['due_date'] ?? null),
            dueDateEnd: DateTimeParser::parseDateTime($data['due_date_end'] ?? null),
            priorityEnum: isset($data['priority_enum']) ? (string) $data['priority_enum'] : null,
            countComments: isset($data['count_comments']) ? (int) $data['count_comments'] : null,
            countSubtasks: isset($data['count_subtasks']) ? (int) $data['count_subtasks'] : null,
            author: isset($data['author']) && is_array($data['author']) ? User::fromArray($data['author']) : null,
            worker: isset($data['worker']) && is_array($data['worker']) ? User::fromArray($data['worker']) : null,
            state: isset($data['state']) && is_array($data['state']) ? State::fromArray($data['state']) : null,
            project: isset($data['project']) && is_array($data['project'])
                ? Project::fromArray($data['project']) : null,
            tasklist: isset($data['tasklist']) && is_array($data['tasklist'])
                ? Tasklist::fromArray($data['tasklist']) : null,
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
