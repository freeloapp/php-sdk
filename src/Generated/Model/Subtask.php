<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * Subtask model.
 */
class Subtask
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $type = null,
        public readonly ?int $taskId = null,
        public readonly ?string $name = null,
        public readonly ?\DateTimeImmutable $dateAdd = null,
        public readonly ?\DateTimeImmutable $dueDate = null,
        public readonly ?\DateTimeImmutable $dueDateEnd = null,
        public readonly ?int $countComments = null,
        public readonly ?int $countSubtasks = null,
        public readonly mixed $author,
        public readonly mixed $worker,
        public readonly mixed $state,
        public readonly mixed $project,
        public readonly mixed $tasklist,
        public readonly array $labels = [],
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
            type: isset($data['type']) ? (string) $data['type'] : null,
            taskId: isset($data['task_id']) ? (int) $data['task_id'] : null,
            name: isset($data['name']) ? (string) $data['name'] : null,
            dateAdd: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['date_add'] ?? null),
            dueDate: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['due_date'] ?? null),
            dueDateEnd: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['due_date_end'] ?? null),
            countComments: isset($data['count_comments']) ? (int) $data['count_comments'] : null,
            countSubtasks: isset($data['count_subtasks']) ? (int) $data['count_subtasks'] : null,
            author: isset($data['author']) ? $data['author'] : null,
            worker: isset($data['worker']) ? $data['worker'] : null,
            state: isset($data['state']) ? $data['state'] : null,
            project: isset($data['project']) ? $data['project'] : null,
            tasklist: isset($data['tasklist']) ? $data['tasklist'] : null,
            labels: isset($data['labels']) && is_array($data['labels'])
                ? $data['labels'] : [],
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
