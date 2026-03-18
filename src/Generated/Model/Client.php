<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

class Client
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $email = null,
        public readonly ?string $name = null,
        public readonly ?string $company = null,
        public readonly ?string $companyId = null,
        public readonly ?string $companyTaxId = null,
        public readonly ?string $street = null,
        public readonly ?string $town = null,
        public readonly ?string $zip = null,
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
            email: isset($data['email']) ? (string) $data['email'] : null,
            name: isset($data['name']) ? (string) $data['name'] : null,
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
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
