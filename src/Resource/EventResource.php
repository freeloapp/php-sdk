<?php

declare(strict_types=1);

namespace Freelo\Sdk\Resource;

use Freelo\Sdk\Exception\ApiException;
use Freelo\Sdk\Http\PaginatedResult;
use Freelo\Sdk\Model\Event;

/**
 * Event resource manager
 *
 * Handles all event-related API operations.
 */
class EventResource extends AbstractResource
{
    protected function getEndpoint(): string
    {
        return 'events';
    }

    /**
     * Get all events - paginated
     *
     * @param array<string, mixed> $filters Available filters:
     *   - projects_ids: int[] - Filter by project IDs
     *   - users_ids: int[] - Filter by user IDs
     *   - events_types: string[] - Filter by event types
     *   - order: string - asc|desc (default: desc)
     *   - date_range: array{date_from?: string, date_to?: string} - Date range filter (Y-m-d format)
     *   - tasks_ids: int[] - Filter by task IDs
     *   - p: int - Page number (0-based)
     * @return PaginatedResult<Event>
     * @throws ApiException
     */
    public function list(array $filters = []): PaginatedResult
    {
        $response = $this->client->get('events', $filters);

        return $this->parser->parsePaginated($response, Event::class);
    }
}
