<?php

declare(strict_types=1);

namespace Freelo\Sdk\Model;

/**
 * Represents a task work entry (time tracking detail)
 */
class TaskWork
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        public readonly int $id,
        public readonly ?string $reported = null,
        public readonly ?int $minutes = null,
        public readonly ?Currency $cost = null,
        public readonly ?string $notice = null,
        public readonly array $data = [],
    ) {
    }

    /**
     * Create a TaskWork from API response data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            reported: isset($data['reported']) ? (string) $data['reported'] : null,
            minutes: isset($data['minutes']) ? (int) $data['minutes'] : null,
            cost: isset($data['cost']) && is_array($data['cost'])
                ? Currency::fromArray($data['cost']) : null,
            notice: isset($data['notice']) ? (string) $data['notice'] : null,
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
