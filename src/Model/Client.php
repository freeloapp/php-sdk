<?php

declare(strict_types=1);

namespace Freelo\Sdk\Model;

/**
 * Represents a Freelo client (company/customer)
 */
class Client
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $email = null,
        public readonly ?string $company = null,
        public readonly ?string $companyId = null,
        public readonly ?string $companyTaxId = null,
        public readonly ?string $street = null,
        public readonly ?string $town = null,
        public readonly ?string $zip = null,
        public readonly array $data = [],
    ) {
    }

    /**
     * Create a Client from API response data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            name: (string) ($data['name'] ?? ''),
            email: isset($data['email']) ? (string) $data['email'] : null,
            company: isset($data['company']) ? (string) $data['company'] : null,
            companyId: isset($data['company_id']) ? (string) $data['company_id'] : null,
            companyTaxId: isset($data['company_tax_id']) ? (string) $data['company_tax_id'] : null,
            street: isset($data['street']) ? (string) $data['street'] : null,
            town: isset($data['town']) ? (string) $data['town'] : null,
            zip: isset($data['zip']) ? (string) $data['zip'] : null,
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
