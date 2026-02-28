<?php

declare(strict_types=1);

namespace Freelo\Sdk\Model;

/**
 * Represents an activity event
 */
class Event
{
    /**
     * @param array<string, mixed>|null $comment
     * @param array<string, mixed>|null $task
     * @param array<string, mixed>|null $taskCheck
     * @param array<string, mixed>|null $document
     * @param array<string, mixed>|null $file
     * @param array<string, mixed> $data
     */
    public function __construct(
        public readonly int $id,
        public readonly string $type,
        public readonly ?string $dateAction = null,
        public readonly ?User $author = null,
        public readonly ?User $who = null,
        public readonly ?array $comment = null,
        public readonly ?array $task = null,
        public readonly ?array $taskCheck = null,
        public readonly ?Tasklist $tasklist = null,
        public readonly ?Project $project = null,
        public readonly ?array $document = null,
        public readonly ?array $file = null,
        public readonly ?string $dueDate = null,
        public readonly ?string $dueDateEnd = null,
        public readonly array $data = [],
    ) {
    }

    /**
     * Create an Event from API response data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            type: isset($data['type']) ? (string) $data['type'] : '',
            dateAction: isset($data['date_action']) ? (string) $data['date_action'] : null,
            author: isset($data['author']) && is_array($data['author']) ? User::fromArray($data['author']) : null,
            who: isset($data['who']) && is_array($data['who']) ? User::fromArray($data['who']) : null,
            comment: isset($data['comment']) && is_array($data['comment']) ? $data['comment'] : null,
            task: isset($data['task']) && is_array($data['task']) ? $data['task'] : null,
            taskCheck: isset($data['task_check']) && is_array($data['task_check']) ? $data['task_check'] : null,
            tasklist: isset($data['tasklist']) && is_array($data['tasklist'])
                ? Tasklist::fromArray($data['tasklist']) : null,
            project: isset($data['project']) && is_array($data['project'])
                ? Project::fromArray($data['project']) : null,
            document: isset($data['document']) && is_array($data['document']) ? $data['document'] : null,
            file: isset($data['file']) && is_array($data['file']) ? $data['file'] : null,
            dueDate: isset($data['due_date']) ? (string) $data['due_date'] : null,
            dueDateEnd: isset($data['due_date_end']) ? (string) $data['due_date_end'] : null,
            data: $data,
        );
    }

    /**
     * Get task ID from task object if available
     */
    public function getTaskId(): ?int
    {
        return isset($this->task['id']) ? (int) $this->task['id'] : null;
    }

    /**
     * Get task name from task object if available
     */
    public function getTaskName(): ?string
    {
        return isset($this->task['name']) ? (string) $this->task['name'] : null;
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
