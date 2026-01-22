<?php

declare(strict_types=1);

namespace Freelo\Sdk\Webhook\Event;

/**
 * Event fired when a task is created
 */
class TaskCreatedEvent extends WebhookEvent
{
    public function getType(): string
    {
        return 'task.created';
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
     * Get the creator user ID
     */
    public function getCreatorId(): ?string
    {
        return $this->get('creator_id');
    }

    /**
     * Get the task creation timestamp
     */
    public function getCreatedAt(): ?string
    {
        return $this->get('created_at');
    }
}
