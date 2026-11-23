<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * ClientUsedInHiddenProjectsError model.
 */
class ClientUsedInHiddenProjectsError
{
    public function __construct(
        public readonly array $errors = [],
        public readonly ?string $error = null,
        public readonly ?int $hiddenProjectsCount = null,
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
            hiddenProjectsCount: isset($data['hidden_projects_count']) ? (int) $data['hidden_projects_count'] : null,
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
