<?php

declare(strict_types=1);

namespace Freelo\Sdk\Model;

/**
 * Represents a basic task reference (id + name only)
 */
class TaskBasic
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly array $data = [],
    ) {
    }

    /**
     * Create a TaskBasic from API response data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            name: isset($data['name']) ? (string) $data['name'] : '',
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
