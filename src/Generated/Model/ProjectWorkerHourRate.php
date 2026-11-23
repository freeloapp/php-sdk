<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * Hourly rate of a user in a project, in the project currency. A user without a rate has `amount: 0` and
 * `is_fixed: false`. `null` when the caller may not see this user's rate.
 *
 * ProjectWorkerHourRate model.
 */
class ProjectWorkerHourRate
{
    public function __construct(
        public readonly int $amount,
        public readonly string $currency,
        public readonly bool $isFixed,
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
            amount: (int) ($data['amount'] ?? 0),
            currency: (string) ($data['currency'] ?? ''),
            isFixed: (bool) ($data['is_fixed'] ?? false),
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
