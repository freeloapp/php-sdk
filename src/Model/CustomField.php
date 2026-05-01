<?php

declare(strict_types=1);

namespace Freelo\Sdk\Model;

use Freelo\Sdk\Internal\DateTimeParser;

/**
 * Represents a custom field (supports CustomField, CustomFieldValue, and CustomFieldWithValue schemas)
 */
class CustomField
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        public readonly string $uuid,
        public readonly string $name,
        public readonly ?string $typeUuid = null,
        public readonly ?int $projectId = null,
        public readonly ?int $authorId = null,
        public readonly ?\DateTimeImmutable $dateAdd = null,
        public readonly ?\DateTimeImmutable $dateEditedAt = null,
        public readonly ?int $priority = null,
        public readonly mixed $value = null,
        public readonly ?string $valueUuid = null,
        public readonly ?int $valueAuthorId = null,
        public readonly ?\DateTimeImmutable $valueDateAdd = null,
        public readonly ?\DateTimeImmutable $valueDateEditedAt = null,
        public readonly ?int $taskId = null,
        public readonly array $data = [],
    ) {
    }

    /**
     * Create a CustomField from API response data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        // Support both CustomField and CustomFieldWithValue schemas
        $uuid = $data['uuid'] ?? $data['field_uuid'] ?? $data['custom_field_uuid'] ?? '';
        $dateAdd = $data['date_add'] ?? $data['field_date_add'] ?? null;

        return new self(
            uuid: (string) $uuid,
            name: isset($data['name']) ? (string) $data['name'] : '',
            typeUuid: isset($data['custom_fields_types_uuid']) ? (string) $data['custom_fields_types_uuid'] : null,
            projectId: isset($data['project_id']) ? (int) $data['project_id'] : null,
            authorId: isset($data['author_id']) ? (int) $data['author_id'] : null,
            dateAdd: DateTimeParser::parseDateTime($dateAdd),
            dateEditedAt: DateTimeParser::parseDateTime($data['date_edited_at'] ?? null),
            priority: isset($data['priority']) ? (int) $data['priority'] : null,
            value: $data['value'] ?? null,
            valueUuid: isset($data['value_uuid']) ? (string) $data['value_uuid'] : null,
            valueAuthorId: isset($data['value_author_id']) ? (int) $data['value_author_id'] : null,
            valueDateAdd: DateTimeParser::parseDateTime($data['value_date_add'] ?? null),
            valueDateEditedAt: DateTimeParser::parseDateTime($data['value_date_edited_at'] ?? null),
            taskId: isset($data['task_id']) ? (int) $data['task_id'] : null,
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
