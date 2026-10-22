<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * LinkCreateRequest model.
 */
class LinkCreateRequest
{
    public function __construct(
        public readonly int $projectId,
        public readonly string $name,
        public readonly string $link,
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
            projectId: (int) ($data['project_id'] ?? 0),
            name: (string) ($data['name'] ?? ''),
            link: (string) ($data['link'] ?? ''),
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
