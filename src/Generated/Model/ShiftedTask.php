<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * ShiftedTask model.
 */
class ShiftedTask
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly \DateTimeImmutable $dueDate,
        public readonly ?\DateTimeImmutable $dueDateEnd,
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
            name: (string) ($data['name'] ?? ''),
            dueDate: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['due_date'] ?? null),
            dueDateEnd: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['due_date_end'] ?? null),
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
