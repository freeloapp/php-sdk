<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * Shape returned by the custom-field create / rename / restore endpoints. It differs from
 * the read shape: the type UUID is exposed as `type`, not as `custom_fields_types_uuid`.
 *
 * CustomFieldMutationResult model.
 */
class CustomFieldMutationResult
{
    public function __construct(
        public readonly ?string $uuid = null,
        public readonly ?string $name = null,
        public readonly ?string $type = null,
        public readonly ?int $projectId = null,
        public readonly ?int $authorId = null,
        public readonly ?\DateTimeImmutable $dateAdd = null,
        public readonly ?int $priority = null,
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
            uuid: isset($data['uuid']) ? (string) $data['uuid'] : null,
            name: isset($data['name']) ? (string) $data['name'] : null,
            type: isset($data['type']) ? (string) $data['type'] : null,
            projectId: isset($data['project_id']) ? (int) $data['project_id'] : null,
            authorId: isset($data['author_id']) ? (int) $data['author_id'] : null,
            dateAdd: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['date_add'] ?? null),
            priority: isset($data['priority']) ? (int) $data['priority'] : null,
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
