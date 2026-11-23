<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * Same as `MultiProjectTask`, with the color and icon of each project.
 *
 * SearchMultiProjectTask model.
 */
class SearchMultiProjectTask
{
    public function __construct(
        public readonly ?bool $isMultiProject = null,
        public readonly array $assignedTo = [],
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
            isMultiProject: isset($data['is_multi_project']) ? (bool) $data['is_multi_project'] : null,
            assignedTo: isset($data['assigned_to']) && is_array($data['assigned_to'])
                ? $data['assigned_to'] : [],
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
