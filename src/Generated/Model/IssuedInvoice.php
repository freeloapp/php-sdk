<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

class IssuedInvoice
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $dateAdd = null,
        public readonly ?string $note = null,
        public readonly ?string $currency = null,
        public readonly ?int $minutes = null,
        public readonly mixed $price,
        public readonly array $subject = [],
        public readonly array $invItems = [],
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
            note: isset($data['note']) ? (string) $data['note'] : null,
            currency: isset($data['currency']) ? (string) $data['currency'] : null,
            minutes: isset($data['minutes']) ? (int) $data['minutes'] : null,
            price: isset($data['price']) ? $data['price'] : null,
            subject: isset($data['subject']) && is_array($data['subject'])
                ? $data['subject'] : [],
            invItems: isset($data['inv_items']) && is_array($data['inv_items'])
                ? $data['inv_items'] : [],
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
