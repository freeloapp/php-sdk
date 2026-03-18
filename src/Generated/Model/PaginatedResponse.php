<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

class PaginatedResponse
{
    public function __construct(
        public readonly ?int $total = null,
        public readonly ?int $count = null,
        public readonly ?int $page = null,
        public readonly ?int $perPage = null,
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
            total: isset($data['total']) ? (int) $data['total'] : null,
            count: isset($data['count']) ? (int) $data['count'] : null,
            page: isset($data['page']) ? (int) $data['page'] : null,
            perPage: isset($data['per_page']) ? (int) $data['per_page'] : null,
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
