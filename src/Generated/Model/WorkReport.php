<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * WorkReport model.
 */
class WorkReport
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?\DateTimeImmutable $dateAdd = null,
        public readonly ?\DateTimeImmutable $dateReported = null,
        public readonly ?string $note = null,
        public readonly ?int $minutes = null,
        public readonly mixed $cost,
        public readonly mixed $author,
        public readonly mixed $worker,
        public readonly array $task = [],
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
            dateAdd: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['date_add'] ?? null),
            dateReported: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['date_reported'] ?? null),
            note: isset($data['note']) ? (string) $data['note'] : null,
            minutes: isset($data['minutes']) ? (int) $data['minutes'] : null,
            cost: isset($data['cost']) ? $data['cost'] : null,
            author: isset($data['author']) ? $data['author'] : null,
            worker: isset($data['worker']) ? $data['worker'] : null,
            task: isset($data['task']) && is_array($data['task'])
                ? $data['task'] : [],
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
