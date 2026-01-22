<?php

declare(strict_types=1);

namespace Freelo\Sdk\Webhook\Event;

/**
 * Event fired when a comment is added
 */
class CommentAddedEvent extends WebhookEvent
{
    public function getType(): string
    {
        return 'comment.added';
    }

    /**
     * Get the comment ID
     */
    public function getCommentId(): ?string
    {
        return $this->get('id');
    }

    /**
     * Get the comment content
     */
    public function getContent(): ?string
    {
        return $this->get('content');
    }

    /**
     * Get the task ID this comment belongs to
     */
    public function getTaskId(): ?string
    {
        return $this->get('task_id');
    }

    /**
     * Get the project ID
     */
    public function getProjectId(): ?string
    {
        return $this->get('project_id');
    }

    /**
     * Get the author user ID
     */
    public function getAuthorId(): ?string
    {
        return $this->get('author_id');
    }

    /**
     * Get the comment creation timestamp
     */
    public function getCreatedAt(): ?string
    {
        return $this->get('created_at');
    }
}
