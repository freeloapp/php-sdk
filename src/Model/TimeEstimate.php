<?php

declare(strict_types=1);

namespace Freelo\Sdk\Model;

/**
 * Represents a time estimate for a task
 */
class TimeEstimate
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        public readonly int $minutes,
        public readonly ?Currency $cost = null,
        public readonly array $data = [],
    ) {
    }

    /**
     * Create a TimeEstimate from API response data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            minutes: (int) ($data['minutes'] ?? 0),
            cost: isset($data['cost']) && is_array($data['cost']) ? Currency::fromArray($data['cost']) : null,
            data: $data,
        );
    }

    /**
     * Get hours from minutes
     */
    public function getHours(): float
    {
        return $this->minutes / 60;
    }

    /**
     * Get formatted time (e.g., "2h 30m")
     */
    public function getFormatted(): string
    {
        $hours = (int) ($this->minutes / 60);
        $mins = $this->minutes % 60;

        if ($hours > 0 && $mins > 0) {
            return "{$hours}h {$mins}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$mins}m";
        }
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
