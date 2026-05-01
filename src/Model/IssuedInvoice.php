<?php

declare(strict_types=1);

namespace Freelo\Sdk\Model;

use Freelo\Sdk\Internal\DateTimeParser;

/**
 * Represents an issued invoice (supports both IssuedInvoice and IssuedInvoiceDetail schemas)
 */
class IssuedInvoice
{
    /**
     * @param array<string, mixed>|null $subject
     * @param array<int, array<string, mixed>> $invItems
     * @param array<string, mixed> $data
     */
    public function __construct(
        public readonly int $id,
        public readonly ?\DateTimeImmutable $dateAdd = null,
        public readonly ?\DateTimeImmutable $dateFrom = null,
        public readonly ?\DateTimeImmutable $dateTo = null,
        public readonly ?string $note = null,
        public readonly ?string $currency = null,
        public readonly ?int $minutes = null,
        public readonly ?Currency $price = null,
        public readonly ?array $subject = null,
        public readonly array $invItems = [],
        public readonly array $data = [],
    ) {
    }

    /**
     * Create an IssuedInvoice from API response data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $invItemsData = isset($data['inv_items']) && is_array($data['inv_items']) ? $data['inv_items'] : [];

        return new self(
            id: (int) ($data['id'] ?? 0),
            dateAdd: DateTimeParser::parseDateTime($data['date_add'] ?? null),
            dateFrom: DateTimeParser::parseDateTime($data['date_from'] ?? null),
            dateTo: DateTimeParser::parseDateTime($data['date_to'] ?? null),
            note: isset($data['note']) ? (string) $data['note'] : null,
            currency: isset($data['currency']) ? (string) $data['currency'] : null,
            minutes: isset($data['minutes']) ? (int) $data['minutes'] : null,
            price: isset($data['price']) && is_array($data['price']) ? Currency::fromArray($data['price']) : null,
            subject: isset($data['subject']) && is_array($data['subject']) ? $data['subject'] : null,
            invItems: $invItemsData,
            data: $data,
        );
    }

    /**
     * Get company name from subject
     */
    public function getCompanyName(): ?string
    {
        return isset($this->subject['company_name']) ? (string) $this->subject['company_name'] : null;
    }

    /**
     * Get invoice URL from subject
     */
    public function getInvoiceUrl(): ?string
    {
        return isset($this->subject['invoice_url']) ? (string) $this->subject['invoice_url'] : null;
    }

    /**
     * Get total hours from minutes
     */
    public function getHours(): float
    {
        return ($this->minutes ?? 0) / 60;
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
