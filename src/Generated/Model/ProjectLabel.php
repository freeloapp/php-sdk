<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

class ProjectLabel
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $name = null,
        public readonly ?string $color = null,
        public readonly ?bool $isPrivate = null,
        public readonly ?int $usersId = null,
        public readonly ?int $usageCount = null,
        public readonly ?bool $canBePublic = null,
        public readonly ?bool $canBeEdited = null,
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
            id: isset($data['id']) ? (int) $data['id'] : null,
            name: isset($data['name']) ? (string) $data['name'] : null,
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
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
