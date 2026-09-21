<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * Shape returned when a subtask is created. It is narrower than the read shape of
 * `GET /task/{task_id}/subtasks` — no project, tasklist, state or counters.
 *
 * SubtaskCreated model.
 */
class SubtaskCreated
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?int $taskId = null,
        public readonly ?string $name = null,
        public readonly ?\DateTimeImmutable $dueDate = null,
        public readonly ?\DateTimeImmutable $dueDateEnd = null,
        public readonly mixed $worker,
        public readonly ?string $priorityEnum = null,
        public readonly array $labels = [],
        public readonly array $comment = [],
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
            taskId: isset($data['task_id']) ? (int) $data['task_id'] : null,
            name: isset($data['name']) ? (string) $data['name'] : null,
            dueDate: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['due_date'] ?? null),
            dueDateEnd: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['due_date_end'] ?? null),
            worker: isset($data['worker']) ? $data['worker'] : null,
            priorityEnum: isset($data['priority_enum']) ? (string) $data['priority_enum'] : null,
            labels: isset($data['labels']) && is_array($data['labels'])
                ? $data['labels'] : [],
            comment: isset($data['comment']) && is_array($data['comment'])
                ? $data['comment'] : [],
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
