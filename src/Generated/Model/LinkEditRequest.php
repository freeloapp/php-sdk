<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * LinkEditRequest model.
 */
class LinkEditRequest
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $link = null,
        public readonly ?string $note = null,
        public readonly ?string $color = null,
        public readonly ?string $directoryUuid = null,
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
            name: isset($data['name']) ? (string) $data['name'] : null,
            link: isset($data['link']) ? (string) $data['link'] : null,
            note: isset($data['note']) ? (string) $data['note'] : null,
            color: isset($data['color']) ? (string) $data['color'] : null,
            directoryUuid: isset($data['directory_uuid']) ? (string) $data['directory_uuid'] : null,
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
