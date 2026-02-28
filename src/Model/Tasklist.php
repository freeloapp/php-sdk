<?php

declare(strict_types=1);

namespace Freelo\Sdk\Model;

/**
 * Represents a Freelo tasklist
 */
class Tasklist
{
    /**
     * @param TaskLabel[] $labels
     * @param array<string, mixed> $data
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $dateAdd = null,
        public readonly ?string $dateEditedAt = null,
        public readonly ?State $state = null,
        public readonly ?Project $project = null,
        public readonly ?Currency $budget = null,
        public readonly ?int $realMinutesSpent = null,
        public readonly ?Currency $realCost = null,
        public readonly array $labels = [],
        public readonly array $data = [],
    ) {
    }

    /**
     * Create a Tasklist from API response data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $labelsData = isset($data['labels']) && is_array($data['labels']) ? $data['labels'] : [];

        return new self(
            id: (int) ($data['id'] ?? 0),
            name: isset($data['name']) ? (string) $data['name'] : '',
            dateAdd: isset($data['date_add']) ? (string) $data['date_add'] : null,
            dateEditedAt: isset($data['date_edited_at']) ? (string) $data['date_edited_at'] : null,
            state: isset($data['state']) && is_array($data['state']) ? State::fromArray($data['state']) : null,
            project: isset($data['project']) && is_array($data['project'])
                ? Project::fromArray($data['project']) : null,
            budget: isset($data['budget']) && is_array($data['budget']) ? Currency::fromArray($data['budget']) : null,
            realMinutesSpent: isset($data['real_minutes_spent']) ? (int) $data['real_minutes_spent'] : null,
            realCost: isset($data['real_cost']) && is_array($data['real_cost'])
                ? Currency::fromArray($data['real_cost']) : null,
            labels: array_map(
                fn (array $l) => TaskLabel::fromArray($l),
                $labelsData
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
