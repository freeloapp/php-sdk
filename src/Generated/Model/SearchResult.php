<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * SearchResult model.
 */
class SearchResult
{
    public function __construct(
        public readonly ?float $score = null,
        public readonly ?int $id = null,
        public readonly ?string $uuid = null,
        public readonly ?string $name = null,
        public readonly ?int $authorId = null,
        public readonly ?string $type = null,
        public readonly array $highlightName = [],
        public readonly array $highlightContent = [],
        public readonly mixed $project,
        public readonly mixed $tasklist,
        public readonly ?int $state = null,
        public readonly ?bool $isSmart = null,
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
            score: isset($data['score']) ? (float) $data['score'] : null,
            id: isset($data['id']) ? (int) $data['id'] : null,
            uuid: isset($data['uuid']) ? (string) $data['uuid'] : null,
            name: isset($data['name']) ? (string) $data['name'] : null,
            authorId: isset($data['author_id']) ? (int) $data['author_id'] : null,
            type: isset($data['type']) ? (string) $data['type'] : null,
            highlightName: isset($data['highlight_name']) && is_array($data['highlight_name'])
                ? $data['highlight_name'] : [],
            highlightContent: isset($data['highlight_content']) && is_array($data['highlight_content'])
                ? $data['highlight_content'] : [],
            project: isset($data['project']) ? $data['project'] : null,
            tasklist: isset($data['tasklist']) ? $data['tasklist'] : null,
            state: isset($data['state']) ? (int) $data['state'] : null,
            isSmart: isset($data['is_smart']) ? (bool) $data['is_smart'] : null,
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
