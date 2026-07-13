<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * TasklistDetail model.
 */
class TasklistDetail
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $name = null,
        public readonly ?int $projectId = null,
        public readonly ?int $workerId = null,
        public readonly ?\DateTimeImmutable $dateAdd = null,
        public readonly ?\DateTimeImmutable $dateEditedAt = null,
        public readonly array $tasks = [],
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
            projectId: isset($data['project_id']) ? (int) $data['project_id'] : null,
            workerId: isset($data['worker_id']) ? (int) $data['worker_id'] : null,
            dateAdd: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['date_add'] ?? null),
            dateEditedAt: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['date_edited_at'] ?? null),
            tasks: isset($data['tasks']) && is_array($data['tasks'])
                ? $data['tasks'] : [],
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
