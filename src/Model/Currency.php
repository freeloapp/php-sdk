<?php

declare(strict_types=1);

namespace Freelo\Sdk\Model;

/**
 * Represents a monetary amount with currency
 */
class Currency
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        public readonly string $amount,
        public readonly string $currency,
        public readonly array $data = [],
    ) {
    }

    /**
     * Create a Currency from API response data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            amount: isset($data['amount']) ? (string) $data['amount'] : '0',
            currency: isset($data['currency']) ? (string) $data['currency'] : 'CZK',
            data: $data,
        );
    }

    /**
     * Get amount as float (divides by 100 for cents)
     */
    public function getDecimalAmount(): float
    {
        return (float) $this->amount / 100;
    }

    /**
     * Get formatted amount with currency symbol
     */
    public function getFormatted(): string
    {
        return number_format($this->getDecimalAmount(), 2) . ' ' . $this->currency;
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
