<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

class Event
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $dateAction = null,
        public readonly ?string $type = null,
        public readonly mixed $author,
        public readonly mixed $who,
        public readonly array $comment = [],
        public readonly array $task = [],
        public readonly array $taskCheck = [],
        public readonly mixed $tasklist,
        public readonly mixed $project,
        public readonly array $document = [],
        public readonly array $file = [],
        public readonly ?string $dueDate = null,
        public readonly ?string $dueDateEnd = null,
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
            dateAction: isset($data['date_action']) ? (string) $data['date_action'] : null,
            type: isset($data['type']) ? (string) $data['type'] : null,
            author: isset($data['author']) ? $data['author'] : null,
            who: isset($data['who']) ? $data['who'] : null,
            comment: isset($data['comment']) && is_array($data['comment'])
                ? $data['comment'] : [],
            task: isset($data['task']) && is_array($data['task'])
                ? $data['task'] : [],
            taskCheck: isset($data['task_check']) && is_array($data['task_check'])
                ? $data['task_check'] : [],
            tasklist: isset($data['tasklist']) ? $data['tasklist'] : null,
            project: isset($data['project']) ? $data['project'] : null,
            document: isset($data['document']) && is_array($data['document'])
                ? $data['document'] : [],
            file: isset($data['file']) && is_array($data['file'])
                ? $data['file'] : [],
            dueDate: isset($data['due_date']) ? (string) $data['due_date'] : null,
            dueDateEnd: isset($data['due_date_end']) ? (string) $data['due_date_end'] : null,
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
