<?php

declare(strict_types=1);

namespace Freelo\Sdk\Resource;

use Freelo\Sdk\Exception\ApiException;
use Freelo\Sdk\Http\PaginatedResult;
use Freelo\Sdk\Model\Subtask;

/**
 * Subtask resource manager
 *
 * Handles all subtask-related API operations.
 */
class SubtaskResource extends AbstractResource
{
    protected function getEndpoint(): string
    {
        return 'subtasks';
    }

    protected function getSingleEndpoint(): string
    {
        return 'subtask';
    }

    /**
     * Get subtasks for a task - paginated
     *
     * @param array<string, mixed> $filters
     * @return PaginatedResult<Subtask>
     * @throws ApiException
     */
    public function list(int $taskId, array $filters = []): PaginatedResult
    {
        $response = $this->client->get("task/{$taskId}/subtasks", $filters);

        return $this->parser->parsePaginated($response, Subtask::class);
    }

    /**
     * Create a subtask
     *
     * @param array<string, mixed> $data
     * @throws ApiException
     */
    public function create(int $taskId, array $data): Subtask
    {
        $response = $this->client->post("task/{$taskId}/subtasks", $data);
        $responseData = $this->parser->parseSingle($response);

        return Subtask::fromArray($responseData);
    }

    /**
     * Edit a simple checklist item (taskcheck)
     *
     * Only name and worker are editable. Applies to simple checklist items only
     * (Subtask with type "taskcheck") - smart subtask IDs return 404, use the
     * task endpoints for those.
     *
     * @param array<string, mixed> $data Available fields:
     *   - name: string - New item name
     *   - worker: int|null - Worker user ID; null clears the assignment
     * @throws ApiException
     */
    public function updateTaskcheck(int $taskcheckId, array $data): bool
    {
        $response = $this->client->post("taskcheck/{$taskcheckId}", $data);

        return $this->parser->parseBoolean($response);
    }

    /**
     * Delete a simple checklist item (taskcheck)
     *
     * Smart subtask IDs return 404 - use the task endpoints for those.
     *
     * @throws ApiException
     */
    public function deleteTaskcheck(int $taskcheckId): bool
    {
        $response = $this->client->delete("taskcheck/{$taskcheckId}");

        return $this->parser->parseBoolean($response);
    }

    /**
     * Mark a simple checklist item (taskcheck) as finished
     *
     * Smart subtask IDs return 404 - use the task endpoints for those.
     *
     * @throws ApiException
     */
    public function finishTaskcheck(int $taskcheckId): bool
    {
        $response = $this->client->post("taskcheck/{$taskcheckId}/finish");

        return $this->parser->parseBoolean($response);
    }

    /**
     * Reopen a finished simple checklist item (taskcheck)
     *
     * Smart subtask IDs return 404 - use the task endpoints for those.
     *
     * @throws ApiException
     */
    public function activateTaskcheck(int $taskcheckId): bool
    {
        $response = $this->client->post("taskcheck/{$taskcheckId}/activate");

        return $this->parser->parseBoolean($response);
    }
}
