<?php

declare(strict_types=1);

namespace Freelo\Sdk\Webhook\Event;

/**
 * Event fired when a project is updated
 */
class ProjectUpdatedEvent extends WebhookEvent
{
    public function getType(): string
    {
        return 'project.updated';
    }

    /**
     * Get the project ID
     */
    public function getProjectId(): ?string
    {
        return $this->get('id');
    }

    /**
     * Get the project name
     */
    public function getProjectName(): ?string
    {
        return $this->get('name');
    }

    /**
     * Get the updated fields
     *
     * @return array<string, mixed>|null
     */
    public function getUpdatedFields(): ?array
    {
        return $this->get('updated_fields');
    }

    /**
     * Get the updater user ID
     */
    public function getUpdaterId(): ?string
    {
        return $this->get('updater_id');
    }

    /**
     * Get the project update timestamp
     */
    public function getUpdatedAt(): ?string
    {
        return $this->get('updated_at');
    }
}
