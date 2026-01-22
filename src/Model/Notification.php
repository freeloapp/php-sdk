<?php

declare(strict_types=1);

namespace Freelo\Sdk\Model;

/**
 * Represents a notification
 */
class Notification
{
    /**
     * @param array<string, mixed>|null $task
     * @param array<string, mixed>|null $comment
     * @param array<string, mixed>|null $document
     * @param array<string, mixed>|null $file
     * @param User[] $moreUsers
     * @param array<string, mixed> $data
     */
    public function __construct(
        public readonly int $id,
        public readonly string $type,
        public readonly ?string $dateAction = null,
        public readonly ?User $author = null,
        public readonly ?User $who = null,
        public readonly ?bool $isUnread = null,
        public readonly ?bool $isNew = null,
        public readonly ?array $task = null,
        public readonly ?Tasklist $tasklist = null,
        public readonly ?Project $project = null,
        public readonly ?array $comment = null,
        public readonly ?array $document = null,
        public readonly ?array $file = null,
        public readonly ?bool $moreComments = null,
        public readonly array $moreUsers = [],
        public readonly array $data = [],
    ) {
    }

    /**
     * Create a Notification from API response data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $moreUsersData = isset($data['more_users']) && is_array($data['more_users']) ? $data['more_users'] : [];

        return new self(
            id: (int) ($data['id'] ?? 0),
            type: isset($data['type']) ? (string) $data['type'] : '',
            dateAction: isset($data['date_action']) ? (string) $data['date_action'] : null,
            author: isset($data['author']) && is_array($data['author']) ? User::fromArray($data['author']) : null,
            who: isset($data['who']) && is_array($data['who']) ? User::fromArray($data['who']) : null,
            isUnread: isset($data['is_unread']) ? (bool) $data['is_unread'] : null,
            isNew: isset($data['is_new']) ? (bool) $data['is_new'] : null,
            task: isset($data['task']) && is_array($data['task']) ? $data['task'] : null,
            tasklist: isset($data['tasklist']) && is_array($data['tasklist']) ? Tasklist::fromArray($data['tasklist']) : null,
            project: isset($data['project']) && is_array($data['project']) ? Project::fromArray($data['project']) : null,
            comment: isset($data['comment']) && is_array($data['comment']) ? $data['comment'] : null,
            document: isset($data['document']) && is_array($data['document']) ? $data['document'] : null,
            file: isset($data['file']) && is_array($data['file']) ? $data['file'] : null,
            moreComments: isset($data['more_comments']) ? (bool) $data['more_comments'] : null,
            moreUsers: array_map(
                fn (array $u) => User::fromArray($u),
                $moreUsersData
            ),
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
     * Check if notification is unread
     */
    public function isUnread(): bool
    {
        return $this->isUnread === true;
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
