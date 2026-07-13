<?php

declare(strict_types=1);

namespace Freelo\Sdk\Model;

/**
 * Represents an accepted task-label color from the fixed palette.
 */
class TaskLabelColor
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        public readonly string $color,
        public readonly string $displayName,
        public readonly bool $isDefault,
        public readonly array $data = [],
    ) {
    }

    /**
     * Create a TaskLabelColor from API response data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            color: isset($data['color']) ? (string) $data['color'] : '',
            displayName: isset($data['display_name']) ? (string) $data['display_name'] : '',
            isDefault: (bool) ($data['is_default'] ?? false),
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
