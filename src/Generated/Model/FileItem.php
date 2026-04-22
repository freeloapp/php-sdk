<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * FileItem model.
 */
class FileItem
{
    public function __construct(
        public readonly ?string $uuid = null,
        public readonly ?string $name = null,
        public readonly mixed $author,
        public readonly mixed $project,
        public readonly ?string $directoryUuid = null,
        public readonly ?string $dateAdd = null,
        public readonly ?int $order = null,
        public readonly ?string $type = null,
        public readonly ?string $filename = null,
        public readonly ?string $caption = null,
        public readonly ?string $mimeType = null,
        public readonly ?string $extension = null,
        public readonly ?int $size = null,
        public readonly ?string $color = null,
        public readonly ?int $itemsCount = null,
        public readonly ?string $link = null,
        public readonly ?string $linkType = null,
        public readonly ?string $note = null,
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
            name: isset($data['name']) ? (string) $data['name'] : null,
            author: isset($data['author']) ? $data['author'] : null,
            project: isset($data['project']) ? $data['project'] : null,
            directoryUuid: isset($data['directory_uuid']) ? (string) $data['directory_uuid'] : null,
            dateAdd: isset($data['date_add']) ? (string) $data['date_add'] : null,
            order: isset($data['order']) ? (int) $data['order'] : null,
            type: isset($data['type']) ? (string) $data['type'] : null,
            filename: isset($data['filename']) ? (string) $data['filename'] : null,
            caption: isset($data['caption']) ? (string) $data['caption'] : null,
            mimeType: isset($data['mime_type']) ? (string) $data['mime_type'] : null,
            extension: isset($data['extension']) ? (string) $data['extension'] : null,
            size: isset($data['size']) ? (int) $data['size'] : null,
            color: isset($data['color']) ? (string) $data['color'] : null,
            itemsCount: isset($data['items_count']) ? (int) $data['items_count'] : null,
            link: isset($data['link']) ? (string) $data['link'] : null,
            linkType: isset($data['link_type']) ? (string) $data['link_type'] : null,
            note: isset($data['note']) ? (string) $data['note'] : null,
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
