<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * UserWithEmail model.
 */
class UserWithEmail
{
    public function __construct(
        public readonly int $id,
        public readonly string $fullname,
        public readonly string $mentionKey,
        public readonly string $email,
        /** @var array<string, mixed> */
        public readonly array $data = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            fullname: (string) ($data['fullname'] ?? ''),
            mentionKey: (string) ($data['mention_key'] ?? ''),
            email: (string) ($data['email'] ?? ''),
            data: $data,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
