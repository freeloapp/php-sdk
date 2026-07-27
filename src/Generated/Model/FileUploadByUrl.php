<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * FileUploadByUrl model.
 */
class FileUploadByUrl
{
    public function __construct(
        public readonly string $downloadUrl,
        public readonly ?string $filename = null,
        public readonly ?string $caption = null,
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
            downloadUrl: (string) ($data['download_url'] ?? ''),
            filename: isset($data['filename']) ? (string) $data['filename'] : null,
            caption: isset($data['caption']) ? (string) $data['caption'] : null,
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
