<?php

declare(strict_types=1);

namespace Freelo\Sdk\Resource;

use Freelo\Sdk\Exception\ApiException;
use Freelo\Sdk\Http\PaginatedResult;
use Freelo\Sdk\Model\Notification;

/**
 * Notification resource manager
 *
 * Handles all notification-related API operations.
 */
class NotificationResource extends AbstractResource
{
    protected function getEndpoint(): string
    {
        return 'all-notifications';
    }

    protected function getSingleEndpoint(): string
    {
        return 'notification';
    }

    /**
     * Get all notifications - paginated
     *
     * @param array<string, mixed> $filters Available filters:
     *   - projects_ids: int[] - Filter by project IDs
     *   - users_ids: int[] - Filter by notification author IDs
     *   - teams_uuids: string[] - Filter by team UUIDs
     *   - order: string - asc|desc (default: desc)
     *   - notification_types: string[] - Filter by notification types
     *   - only_unread: bool - Only unread notifications (default: false)
     *   - p: int - Page number (0-based)
     * @return PaginatedResult<Notification>
     * @throws ApiException
     */
    public function list(array $filters = []): PaginatedResult
    {
        $response = $this->client->get('all-notifications', $filters);

        return $this->parser->parsePaginated($response, Notification::class);
    }

    /**
     * Mark notification as read
     *
     * @throws ApiException
     */
    public function markAsRead(int $notificationId): bool
    {
        $response = $this->client->post("notification/{$notificationId}/mark-as-read");

        return $this->parser->parseBoolean($response);
    }

    /**
     * Mark notification as unread
     *
     * @throws ApiException
     */
    public function markAsUnread(int $notificationId): bool
    {
        $response = $this->client->post("notification/{$notificationId}/mark-as-unread");

        return $this->parser->parseBoolean($response);
    }
}
