<?php

declare(strict_types=1);

namespace Freelo\Sdk\Model;

use Freelo\Sdk\Internal\DateTimeParser;

/**
 * Represents a Freelo project
 */
class Project
{
    /**
     * @param Tasklist[] $tasklists
     * @param User[] $workers
     * @param array<string, mixed> $data
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?\DateTimeImmutable $dateAdd = null,
        public readonly ?\DateTimeImmutable $dateEditedAt = null,
        public readonly ?User $owner = null,
        public readonly ?State $state = null,
        public readonly ?int $minutesBudget = null,
        public readonly ?Currency $budget = null,
        public readonly ?int $realMinutesSpent = null,
        public readonly ?Currency $realCost = null,
        public readonly ?Client $client = null,
        public readonly array $tasklists = [],
        public readonly array $workers = [],
        public readonly array $data = [],
    ) {
    }

    /**
     * Create a Project from API response data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $tasklistsData = isset($data['tasklists']) && is_array($data['tasklists']) ? $data['tasklists'] : [];
        $workersData = isset($data['workers']) && is_array($data['workers']) ? $data['workers'] : [];

        return new self(
            id: (int) ($data['id'] ?? 0),
            name: isset($data['name']) ? (string) $data['name'] : '',
            dateAdd: DateTimeParser::parseDateTime($data['date_add'] ?? null),
            dateEditedAt: DateTimeParser::parseDateTime($data['date_edited_at'] ?? null),
            owner: isset($data['owner']) && is_array($data['owner']) ? User::fromArray($data['owner']) : null,
            state: isset($data['state']) && is_array($data['state']) ? State::fromArray($data['state']) : null,
            minutesBudget: isset($data['minutes_budget']) ? (int) $data['minutes_budget'] : null,
            budget: isset($data['budget']) && is_array($data['budget']) ? Currency::fromArray($data['budget']) : null,
            realMinutesSpent: isset($data['real_minutes_spent']) ? (int) $data['real_minutes_spent'] : null,
            realCost: isset($data['real_cost']) && is_array($data['real_cost'])
                ? Currency::fromArray($data['real_cost']) : null,
            client: isset($data['client']) && is_array($data['client']) ? Client::fromArray($data['client']) : null,
            tasklists: array_map(
                fn (array $t) => Tasklist::fromArray($t),
                $tasklistsData
            ),
            workers: array_map(
                fn (array $w) => User::fromArray($w),
                $workersData
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
