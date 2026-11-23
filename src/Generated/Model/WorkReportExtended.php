<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * Work report row of an invoice (`/issued-invoice/{invoice_id}/reports-json`) — a snapshot of the work report
 * taken when the invoice was created, not the work report itself. Only rows listed in the detail's
 * `inv_items[].reports[]` are billed by the invoice; the others are not invoiced.
 *
 * WorkReportExtended model.
 */
class WorkReportExtended
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?int $workReportId = null,
        public readonly ?\DateTimeImmutable $dateAdd = null,
        public readonly ?\DateTimeImmutable $dateReported = null,
        public readonly ?string $note = null,
        public readonly ?int $minutes = null,
        public readonly mixed $cost,
        public readonly array $author = [],
        public readonly array $worker = [],
        public readonly mixed $task,
        public readonly array $project = [],
        public readonly array $tasklist = [],
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
            workReportId: isset($data['work_report_id']) ? (int) $data['work_report_id'] : null,
            dateAdd: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['date_add'] ?? null),
            dateReported: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['date_reported'] ?? null),
            note: isset($data['note']) ? (string) $data['note'] : null,
            minutes: isset($data['minutes']) ? (int) $data['minutes'] : null,
            cost: isset($data['cost']) ? $data['cost'] : null,
            author: isset($data['author']) && is_array($data['author'])
                ? $data['author'] : [],
            worker: isset($data['worker']) && is_array($data['worker'])
                ? $data['worker'] : [],
            task: isset($data['task']) ? $data['task'] : null,
            project: isset($data['project']) && is_array($data['project'])
                ? $data['project'] : [],
            tasklist: isset($data['tasklist']) && is_array($data['tasklist'])
                ? $data['tasklist'] : [],
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
