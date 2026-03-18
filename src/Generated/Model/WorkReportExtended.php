<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

class WorkReportExtended
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $dateAdd = null,
        public readonly ?string $dateReported = null,
        public readonly ?string $note = null,
        public readonly ?int $minutes = null,
        public readonly mixed $cost,
        public readonly mixed $author,
        public readonly mixed $worker,
        public readonly array $task = [],
        public readonly mixed $project,
        public readonly mixed $tasklist,
        public readonly ?int $workReportId = null,
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
            dateAdd: isset($data['date_add']) ? (string) $data['date_add'] : null,
            dateReported: isset($data['date_reported']) ? (string) $data['date_reported'] : null,
            note: isset($data['note']) ? (string) $data['note'] : null,
            minutes: isset($data['minutes']) ? (int) $data['minutes'] : null,
            cost: isset($data['cost']) ? $data['cost'] : null,
            author: isset($data['author']) ? $data['author'] : null,
            worker: isset($data['worker']) ? $data['worker'] : null,
            task: isset($data['task']) && is_array($data['task'])
                ? $data['task'] : [],
            project: isset($data['project']) ? $data['project'] : null,
            tasklist: isset($data['tasklist']) ? $data['tasklist'] : null,
            workReportId: isset($data['work_report_id']) ? (int) $data['work_report_id'] : null,
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
