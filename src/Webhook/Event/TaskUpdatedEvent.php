<?php

declare(strict_types=1);

namespace Freelo\Sdk\Webhook\Event;

/**
 * Event fired when a task is updated
 */
class TaskUpdatedEvent extends WebhookEvent
{
    public function getType(): string
    {
        return 'task.updated';
    }

    /**
     * Get the task ID
     */
    public function getTaskId(): ?string
    {
        return $this->get('id');
    }

    /**
     * Get the task name
     */
    public function getTaskName(): ?string
    {
        return $this->get('name');
    }

    /**
     * Get the project ID
     */
    public function getProjectId(): ?string
    {
        return $this->get('project_id');
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
     * Get the task update timestamp
     */
    public function getUpdatedAt(): ?string
    {
        return $this->get('updated_at');
    }
}
