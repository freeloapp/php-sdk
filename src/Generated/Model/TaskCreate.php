<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

class TaskCreate
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $dueDate = null,
        public readonly ?string $dueDateEnd = null,
        public readonly ?int $worker = null,
        public readonly ?string $priorityEnum = null,
        public readonly array $comment = [],
        public readonly array $labels = [],
        public readonly array $trackingUsersIds = [],
        public readonly ?bool $turnOffAuthorsTracking = null,
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
            name: (string) ($data['name'] ?? ''),
            dueDate: isset($data['due_date']) ? (string) $data['due_date'] : null,
            dueDateEnd: isset($data['due_date_end']) ? (string) $data['due_date_end'] : null,
            worker: isset($data['worker']) ? (int) $data['worker'] : null,
            priorityEnum: isset($data['priority_enum']) ? (string) $data['priority_enum'] : null,
            comment: isset($data['comment']) && is_array($data['comment'])
                ? $data['comment'] : [],
            labels: isset($data['labels']) && is_array($data['labels'])
                ? $data['labels'] : [],
            trackingUsersIds: isset($data['tracking_users_ids']) && is_array($data['tracking_users_ids'])
                ? $data['tracking_users_ids'] : [],
            turnOffAuthorsTracking: isset($data['turn_off_authors_tracking']) ? (bool) $data['turn_off_authors_tracking'] : null,
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
