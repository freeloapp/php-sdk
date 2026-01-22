<?php

declare(strict_types=1);

namespace Freelo\Sdk\Model;

/**
 * Represents a state object (for tasks, projects, etc.)
 */
class State
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        public readonly int $id,
        public readonly string $state,
        public readonly array $data = [],
    ) {
    }

    /**
     * Create a State from API response data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            state: isset($data['state']) ? (string) $data['state'] : '',
            data: $data,
        );
    }

    /**
     * Check if the state is active
     */
    public function isActive(): bool
    {
        return $this->state === 'active';
    }

    /**
     * Check if the state is archived
     */
    public function isArchived(): bool
    {
        return $this->state === 'archived';
    }

    /**
     * Check if the state is finished
     */
    public function isFinished(): bool
    {
        return $this->state === 'finished';
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
