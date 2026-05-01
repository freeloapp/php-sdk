<?php

declare(strict_types=1);

namespace Freelo\Sdk\Model;

use Freelo\Sdk\Internal\DateTimeParser;

/**
 * Represents a work/time report entry
 */
class WorkReport
{
    /**
     * @param array<string, mixed>|null $task
     * @param array<string, mixed>|null $project
     * @param array<string, mixed> $data
     */
    public function __construct(
        public readonly int $id,
        public readonly ?int $minutes = null,
        public readonly ?string $note = null,
        public readonly ?\DateTimeImmutable $dateAdd = null,
        public readonly ?\DateTimeImmutable $dateEditedAt = null,
        public readonly ?\DateTimeImmutable $dateReported = null,
        public readonly ?User $author = null,
        public readonly ?User $worker = null,
        public readonly ?Currency $cost = null,
        public readonly ?array $task = null,
        public readonly ?Tasklist $tasklist = null,
        public readonly ?array $project = null,
        public readonly array $data = [],
    ) {
    }

    /**
     * Create a WorkReport from API response data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        // Support both 'worker' and 'user' fields for worker (API may return either)
        $workerData = $data['worker'] ?? $data['user'] ?? null;

        return new self(
            id: (int) ($data['id'] ?? 0),
            minutes: isset($data['minutes']) ? (int) $data['minutes'] : null,
            note: isset($data['note']) ? (string) $data['note'] : null,
            dateAdd: DateTimeParser::parseDateTime($data['date_add'] ?? null),
            dateEditedAt: DateTimeParser::parseDateTime($data['date_edited_at'] ?? null),
            dateReported: DateTimeParser::parseDateTime($data['date_reported'] ?? null),
            author: isset($data['author']) && is_array($data['author'])
                ? User::fromArray($data['author']) : null,
            worker: is_array($workerData) ? User::fromArray($workerData) : null,
            cost: isset($data['cost']) && is_array($data['cost']) ? Currency::fromArray($data['cost']) : null,
            task: isset($data['task']) && is_array($data['task']) ? $data['task'] : null,
            tasklist: isset($data['tasklist']) && is_array($data['tasklist'])
                ? Tasklist::fromArray($data['tasklist']) : null,
            project: isset($data['project']) && is_array($data['project']) ? $data['project'] : null,
            data: $data,
        );
    }

    /**
     * Get hours from minutes
     */
    public function getHours(): float
    {
        return ($this->minutes ?? 0) / 60;
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
     * Get project ID from project object if available
     */
    public function getProjectId(): ?int
    {
        return isset($this->project['id']) ? (int) $this->project['id'] : null;
    }

    /**
     * Get project name from project object if available
     */
    public function getProjectName(): ?string
    {
        return isset($this->project['name']) ? (string) $this->project['name'] : null;
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
