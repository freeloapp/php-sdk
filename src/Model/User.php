<?php

declare(strict_types=1);

namespace Freelo\Sdk\Model;

/**
 * Represents a Freelo user/worker
 */
class User
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        public readonly int $id,
        public readonly string $fullname,
        public readonly ?string $email = null,
        public readonly ?HourRate $hourRate = null,
        public readonly array $data = [],
    ) {
    }

    /**
     * Create a User from API response data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            fullname: isset($data['fullname']) ? (string) $data['fullname'] : '',
            email: isset($data['email']) ? (string) $data['email'] : null,
            hourRate: isset($data['hour_rate']) && is_array($data['hour_rate'])
                ? HourRate::fromArray($data['hour_rate']) : null,
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
