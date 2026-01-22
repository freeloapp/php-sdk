<?php

declare(strict_types=1);

namespace Freelo\Sdk\Model;

/**
 * Represents a Freelo report (generic report type)
 */
class Report
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        public readonly int $id,
        public readonly string $type,
        public readonly ?string $format = null,
        public readonly ?string $url = null,
        public readonly ?string $dateAdd = null,
        public readonly array $data = [],
    ) {
    }

    /**
     * Create a Report from API response data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            type: isset($data['type']) ? (string) $data['type'] : '',
            format: isset($data['format']) ? (string) $data['format'] : null,
            url: isset($data['url']) ? (string) $data['url'] : null,
            dateAdd: isset($data['date_add']) ? (string) $data['date_add'] : null,
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
