<?php

declare(strict_types=1);

namespace Freelo\Sdk\Model;

/**
 * Represents a project label
 */
class ProjectLabel
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $color = null,
        public readonly ?bool $isPrivate = null,
        public readonly ?int $usersId = null,
        public readonly ?int $usageCount = null,
        public readonly ?bool $canBePublic = null,
        public readonly ?bool $canBeEdited = null,
        public readonly array $data = [],
    ) {
    }

    /**
     * Create a ProjectLabel from API response data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            name: isset($data['name']) ? (string) $data['name'] : '',
            color: isset($data['color']) ? (string) $data['color'] : null,
            isPrivate: isset($data['is_private']) ? (bool) $data['is_private'] : null,
            usersId: isset($data['users_id']) ? (int) $data['users_id'] : null,
            usageCount: isset($data['usage_count']) ? (int) $data['usage_count'] : null,
            canBePublic: isset($data['can_be_public']) ? (bool) $data['can_be_public'] : null,
            canBeEdited: isset($data['can_be_edited']) ? (bool) $data['can_be_edited'] : null,
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
