<?php

declare(strict_types=1);

namespace Freelo\Sdk\Model;

/**
 * Represents a task label (uses UUID)
 */
class TaskLabel
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        public readonly string $uuid,
        public readonly string $name,
        public readonly ?string $color = null,
        public readonly array $data = [],
    ) {
    }

    /**
     * Create a TaskLabel from API response data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            uuid: isset($data['uuid']) ? (string) $data['uuid'] : '',
            name: isset($data['name']) ? (string) $data['name'] : '',
            color: isset($data['color']) ? (string) $data['color'] : null,
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
