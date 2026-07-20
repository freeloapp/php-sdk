<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * A file to attach to a comment / task description / work report. Provide **one** of two variants:
 * - **By uuid** — reference a file already uploaded via `POST /file/upload` (`{ "uuid": "…" }`).
 * - **By download_url** — give a URL that Freelo downloads the file from server-side (`{ "download_url": "…" }`).
 *
 * The variant is chosen by which key is present: if `uuid` is set it is used, otherwise `download_url` is downloaded. `caption` is optional in both.
 *
 * FileUpload model.
 */
class FileUpload
{
    public function __construct(
        public readonly ?string $uuid = null,
        public readonly ?string $caption = null,
        public readonly ?string $downloadUrl = null,
        public readonly ?string $filename = null,
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
            uuid: isset($data['uuid']) ? (string) $data['uuid'] : null,
            caption: isset($data['caption']) ? (string) $data['caption'] : null,
            downloadUrl: isset($data['download_url']) ? (string) $data['download_url'] : null,
            filename: isset($data['filename']) ? (string) $data['filename'] : null,
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
