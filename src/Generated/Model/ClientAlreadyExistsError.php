<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * ClientAlreadyExistsError model.
 */
class ClientAlreadyExistsError
{
    public function __construct(
        public readonly array $errors = [],
        public readonly ?string $error = null,
        public readonly ?int $existingClientId = null,
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
            errors: isset($data['errors']) && is_array($data['errors'])
                ? $data['errors'] : [],
            error: isset($data['error']) ? (string) $data['error'] : null,
            existingClientId: isset($data['existing_client_id']) ? (int) $data['existing_client_id'] : null,
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
