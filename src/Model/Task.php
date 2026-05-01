<?php

declare(strict_types=1);

namespace Freelo\Sdk\Model;

use Freelo\Sdk\Internal\DateTimeParser;

/**
 * Represents a Freelo task
 */
class Task
{
    /**
     * @param TaskLabel[] $labels
     * @param Comment[] $comments
     * @param CustomField[] $customFields
     * @param User[] $trackingUsers
     * @param array<string, mixed> $usersTimeEstimates
     * @param array<string, mixed> $data
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?\DateTimeImmutable $dateAdd = null,
        public readonly ?\DateTimeImmutable $dateEditedAt = null,
        public readonly ?\DateTimeImmutable $dueDate = null,
        public readonly ?\DateTimeImmutable $dueDateEnd = null,
        public readonly ?\DateTimeImmutable $dateFinished = null,
        public readonly ?string $priorityEnum = null,
        public readonly ?int $minutes = null,
        public readonly ?int $countComments = null,
        public readonly ?int $countSubtasks = null,
        public readonly ?int $finishedSubtasksCount = null,
        public readonly ?int $parentTaskId = null,
        public readonly ?User $author = null,
        public readonly ?User $worker = null,
        public readonly ?User $finishedBy = null,
        public readonly ?State $state = null,
        public readonly ?Project $project = null,
        public readonly ?Tasklist $tasklist = null,
        public readonly ?Currency $cost = null,
        public readonly ?TimeEstimate $totalTimeEstimate = null,
        public readonly array $labels = [],
        public readonly array $comments = [],
        public readonly array $customFields = [],
        public readonly array $trackingUsers = [],
        public readonly array $usersTimeEstimates = [],
        public readonly array $data = [],
    ) {
    }

    /**
     * Create a Task from API response data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $labelsData = isset($data['labels']) && is_array($data['labels']) ? $data['labels'] : [];
        $commentsData = isset($data['comments']) && is_array($data['comments']) ? $data['comments'] : [];
        $customFieldsData = isset($data['custom_fields']) && is_array($data['custom_fields'])
            ? $data['custom_fields'] : [];
        $trackingUsersData = isset($data['tracking_users']) && is_array($data['tracking_users'])
            ? $data['tracking_users'] : [];

        return new self(
            id: (int) ($data['id'] ?? 0),
            name: isset($data['name']) ? (string) $data['name'] : '',
            dateAdd: DateTimeParser::parseDateTime($data['date_add'] ?? null),
            dateEditedAt: DateTimeParser::parseDateTime($data['date_edited_at'] ?? null),
            dueDate: DateTimeParser::parseDateTime($data['due_date'] ?? null),
            dueDateEnd: DateTimeParser::parseDateTime($data['due_date_end'] ?? null),
            dateFinished: DateTimeParser::parseDateTime($data['date_finished'] ?? null),
            priorityEnum: isset($data['priority_enum']) ? (string) $data['priority_enum'] : null,
            minutes: isset($data['minutes']) ? (int) $data['minutes'] : null,
            countComments: isset($data['count_comments']) ? (int) $data['count_comments'] : null,
            countSubtasks: isset($data['count_subtasks']) ? (int) $data['count_subtasks'] : null,
            finishedSubtasksCount: isset($data['finished_subtasks_count'])
                ? (int) $data['finished_subtasks_count'] : null,
            parentTaskId: isset($data['parent_task_id']) ? (int) $data['parent_task_id'] : null,
            author: isset($data['author']) && is_array($data['author'])
                ? User::fromArray($data['author']) : null,
            worker: isset($data['worker']) && is_array($data['worker'])
                ? User::fromArray($data['worker']) : null,
            finishedBy: isset($data['finished_by']) && is_array($data['finished_by'])
                ? User::fromArray($data['finished_by']) : null,
            state: isset($data['state']) && is_array($data['state'])
                ? State::fromArray($data['state']) : null,
            project: isset($data['project']) && is_array($data['project'])
                ? Project::fromArray($data['project']) : null,
            tasklist: isset($data['tasklist']) && is_array($data['tasklist'])
                ? Tasklist::fromArray($data['tasklist']) : null,
            cost: isset($data['cost']) && is_array($data['cost'])
                ? Currency::fromArray($data['cost']) : null,
            totalTimeEstimate: isset($data['total_time_estimate'])
                && is_array($data['total_time_estimate'])
                ? TimeEstimate::fromArray($data['total_time_estimate']) : null,
            labels: array_map(
                fn (array $l) => TaskLabel::fromArray($l),
                $labelsData
            ),
            comments: array_map(
                fn (array $c) => Comment::fromArray($c),
                $commentsData
            ),
            customFields: array_map(
                fn (array $cf) => CustomField::fromArray($cf),
                $customFieldsData
            ),
            trackingUsers: array_map(
                fn (array $u) => User::fromArray($u),
                $trackingUsersData
            ),
            usersTimeEstimates: isset($data['users_time_estimates'])
                && is_array($data['users_time_estimates'])
                ? $data['users_time_estimates'] : [],
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
