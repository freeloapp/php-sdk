<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

class TaskCreated
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $name = null,
        public readonly ?string $dateAdd = null,
        public readonly ?string $dueDate = null,
        public readonly ?string $dueDateEnd = null,
        public readonly mixed $worker,
        public readonly ?string $priorityEnum = null,
        public readonly array $labels = [],
        public readonly array $trackingUsers = [],
        public readonly array $subtasks = [],
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
            dueDate: isset($data['due_date']) ? (string) $data['due_date'] : null,
            dueDateEnd: isset($data['due_date_end']) ? (string) $data['due_date_end'] : null,
            worker: isset($data['worker']) ? $data['worker'] : null,
            priorityEnum: isset($data['priority_enum']) ? (string) $data['priority_enum'] : null,
            labels: isset($data['labels']) && is_array($data['labels'])
                ? $data['labels'] : [],
            trackingUsers: isset($data['tracking_users']) && is_array($data['tracking_users'])
                ? $data['tracking_users'] : [],
            subtasks: isset($data['subtasks']) && is_array($data['subtasks'])
                ? $data['subtasks'] : [],
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
