<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * ProjectWithTasklists model.
 */
class ProjectWithTasklists
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $name = null,
        public readonly ?\DateTimeImmutable $dateAdd = null,
        public readonly ?\DateTimeImmutable $dateEditedAt = null,
        public readonly array $tasklists = [],
        public readonly mixed $client,
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
            dateAdd: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['date_add'] ?? null),
            dateEditedAt: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['date_edited_at'] ?? null),
            tasklists: isset($data['tasklists']) && is_array($data['tasklists'])
                ? $data['tasklists'] : [],
            client: isset($data['client']) ? $data['client'] : null,
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
