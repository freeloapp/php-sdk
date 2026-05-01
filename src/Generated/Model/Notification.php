<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\Sdk\Generated\Model;

/**
 * Notification model.
 */
class Notification
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $type = null,
        public readonly ?\DateTimeImmutable $dateAction = null,
        public readonly mixed $author,
        public readonly mixed $who,
        public readonly ?bool $isUnread = null,
        public readonly ?bool $isNew = null,
        public readonly array $task = [],
        public readonly mixed $tasklist,
        public readonly mixed $project,
        public readonly array $comment = [],
        public readonly array $document = [],
        public readonly array $file = [],
        public readonly ?bool $moreComments = null,
        public readonly array $moreUsers = [],
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
            id: isset($data['id']) ? (int) $data['id'] : null,
            type: isset($data['type']) ? (string) $data['type'] : null,
            dateAction: \Freelo\Sdk\Internal\DateTimeParser::parseDateTime($data['date_action'] ?? null),
            author: isset($data['author']) ? $data['author'] : null,
            who: isset($data['who']) ? $data['who'] : null,
            isUnread: isset($data['is_unread']) ? (bool) $data['is_unread'] : null,
            isNew: isset($data['is_new']) ? (bool) $data['is_new'] : null,
            task: isset($data['task']) && is_array($data['task'])
                ? $data['task'] : [],
            tasklist: isset($data['tasklist']) ? $data['tasklist'] : null,
            project: isset($data['project']) ? $data['project'] : null,
            comment: isset($data['comment']) && is_array($data['comment'])
                ? $data['comment'] : [],
            document: isset($data['document']) && is_array($data['document'])
                ? $data['document'] : [],
            file: isset($data['file']) && is_array($data['file'])
                ? $data['file'] : [],
            moreComments: isset($data['more_comments']) ? (bool) $data['more_comments'] : null,
            moreUsers: isset($data['more_users']) && is_array($data['more_users'])
                ? $data['more_users'] : [],
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
