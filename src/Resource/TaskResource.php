<?php

declare(strict_types=1);

namespace Freelo\Sdk\Resource;

use Freelo\Sdk\Exception\ApiException;
use Freelo\Sdk\Http\PaginatedResult;
use Freelo\Sdk\Model\Comment;
use Freelo\Sdk\Model\Task;

/**
 * Task resource manager
 *
 * Handles all task-related API operations.
 */
class TaskResource extends AbstractResource
{
    protected function getEndpoint(): string
    {
        return 'tasks';
    }

    protected function getSingleEndpoint(): string
    {
        return 'task';
    }

    /**
     * Get all tasks - paginated
     *
     * @param array<string, mixed> $filters Available filters:
     *   - search_query: string - Fulltext search for task name
     *   - state_id: int - Filter by task state ID
     *   - projects_ids: int[] - Filter by project IDs
     *   - tasklists_ids: int[] - Filter by tasklist IDs
     *   - worker_id: int - Filter by assigned worker
     *   - with_label: string - Include tasks with label (case insensitive)
     *   - without_label: string - Exclude tasks with label
     *   - no_due_date: bool - Only tasks without due date
     *   - due_date_range: array{date_from?: string, date_to?: string} - Due date range (Y-m-d format)
     *   - finished_date_range: array{date_from?: string, date_to?: string} - Finished date range
     *   - finished_overdue: bool - Only tasks finished after due date
     *   - order_by: string - priority|name|date_add|date_edited_at
     *   - order: string - asc|desc
     *   - p: int - Page number (0-based)
     * @return PaginatedResult<Task>
     * @throws ApiException
     */
    public function getAll(array $filters = []): PaginatedResult
    {
        $response = $this->client->get('all-tasks', $filters);

        return $this->parser->parsePaginated($response, Task::class);
    }

    /**
     * Get tasks filtered by project
     *
     * Convenience method that wraps getAll() with project filter.
     *
     * @param array<string, mixed> $filters Additional filters (see getAll() for options)
     * @return PaginatedResult<Task>
     * @throws ApiException
     */
    public function listByProject(int $projectId, array $filters = []): PaginatedResult
    {
        $filters['projects_ids'] = [$projectId];

        return $this->getAll($filters);
    }

    /**
     * Get tasks assigned to a specific worker
     *
     * Convenience method that wraps getAll() with worker filter.
     *
     * @param array<string, mixed> $filters Additional filters (see getAll() for options)
     * @return PaginatedResult<Task>
     * @throws ApiException
     */
    public function listByWorker(int $userId, array $filters = []): PaginatedResult
    {
        $filters['worker_id'] = $userId;

        return $this->getAll($filters);
    }

    /**
     * Get overdue tasks (due date in the past, not finished)
     *
     * Convenience method that wraps getAll() with due date filter set to today.
     *
     * @param array<string, mixed> $filters Additional filters (see getAll() for options)
     * @return PaginatedResult<Task>
     * @throws ApiException
     */
    public function listOverdue(array $filters = []): PaginatedResult
    {
        $filters['due_date_range'] = [
            'date_to' => date('Y-m-d'),
        ];

        return $this->getAll($filters);
    }

    /**
     * Get tasks in a specific tasklist
     *
     * @param array<string, mixed> $filters
     * @return Task[]
     * @throws ApiException
     */
    public function listInTasklist(int $projectId, int $tasklistId, array $filters = []): array
    {
        $response = $this->client->get(
            "project/{$projectId}/tasklist/{$tasklistId}/tasks",
            $filters
        );
        $data = $this->parser->parseCollection($response);

        return array_map(
            fn(array $item) => Task::fromArray($item),
            $data
        );
    }

    /**
     * Get finished tasks in a tasklist - paginated
     *
     * @param array<string, mixed> $filters
     * @return PaginatedResult<Task>
     * @throws ApiException
     */
    public function getFinished(int $tasklistId, array $filters = []): PaginatedResult
    {
        $response = $this->client->get("tasklist/{$tasklistId}/finished-tasks", $filters);

        return $this->parser->parsePaginated($response, Task::class);
    }

    /**
     * Get a specific task by ID
     *
     * @throws ApiException
     */
    public function get(int $taskId): Task
    {
        $response = $this->client->get("task/{$taskId}");
        $data = $this->parser->parseSingle($response);

        return Task::fromArray($data);
    }

    /**
     * Create a new task in a tasklist
     *
     * @param array<string, mixed> $data
     * @throws ApiException
     */
    public function create(int $projectId, int $tasklistId, array $data): Task
    {
        $response = $this->client->post(
            "project/{$projectId}/tasklist/{$tasklistId}/tasks",
            $data
        );
        $responseData = $this->parser->parseSingle($response);

        return Task::fromArray($responseData);
    }

    /**
     * Create task from template
     *
     * @param array<string, mixed> $data
     * @throws ApiException
     */
    public function createFromTemplate(int $templateId, array $data): Task
    {
        $response = $this->client->post("task/create-from-template/{$templateId}", $data);
        $responseData = $this->parser->parseSingle($response);

        return Task::fromArray($responseData);
    }

    /**
     * Update a task
     *
     * @param array<string, mixed> $data
     * @throws ApiException
     */
    public function update(int $taskId, array $data): Task
    {
        $response = $this->client->post("task/{$taskId}", $data);
        $responseData = $this->parser->parseSingle($response);

        return Task::fromArray($responseData);
    }

    /**
     * Delete a task
     *
     * @throws ApiException
     */
    public function delete(int $taskId): bool
    {
        $response = $this->client->delete("task/{$taskId}");

        return $this->parser->parseBoolean($response);
    }

    /**
     * Finish a task
     *
     * @throws ApiException
     */
    public function finish(int $taskId): bool
    {
        $response = $this->client->post("task/{$taskId}/finish");

        return $this->parser->parseBoolean($response);
    }

    /**
     * Activate a finished task
     *
     * @throws ApiException
     */
    public function activate(int $taskId): bool
    {
        $response = $this->client->post("task/{$taskId}/activate");

        return $this->parser->parseBoolean($response);
    }

    /**
     * Move task to another tasklist
     *
     * @param array<string, mixed> $options
     * @throws ApiException
     */
    public function move(int $taskId, int $tasklistId, array $options = []): bool
    {
        $response = $this->client->post("task/{$taskId}/move/{$tasklistId}", $options);

        return $this->parser->parseBoolean($response);
    }

    /**
     * Get task description
     *
     * @throws ApiException
     */
    public function getDescription(int $taskId): ?Comment
    {
        $response = $this->client->get("task/{$taskId}/description");
        $data = $this->parser->parseSingle($response);

        if (empty($data)) {
            return null;
        }

        return Comment::fromArray($data);
    }

    /**
     * Set/update task description
     *
     * @param string[] $files File UUIDs to attach
     * @throws ApiException
     */
    public function setDescription(int $taskId, string $content, array $files = []): Comment
    {
        $data = ['content' => $content];
        if (!empty($files)) {
            $data['files'] = $files;
        }

        $response = $this->client->post("task/{$taskId}/description", $data);
        $responseData = $this->parser->parseSingle($response);

        return Comment::fromArray($responseData);
    }

    /**
     * Create a task reminder
     *
     * @return array<string, mixed>
     * @throws ApiException
     */
    public function createReminder(int $taskId, string $remindAt): array
    {
        $response = $this->client->post("task/{$taskId}/reminder", [
            'remind_at' => $remindAt,
        ]);

        return $this->parser->parseSingle($response);
    }

    /**
     * Delete task reminder
     *
     * @throws ApiException
     */
    public function deleteReminder(int $taskId): bool
    {
        $response = $this->client->delete("task/{$taskId}/reminder");

        return $this->parser->parseBoolean($response);
    }

    /**
     * Get public link to task
     *
     * @throws ApiException
     */
    public function getPublicLink(int $taskId): ?string
    {
        $response = $this->client->get("public-link/task/{$taskId}");
        $data = $this->parser->parseSingle($response);

        return $data['url'] ?? null;
    }

    /**
     * Delete public link to task
     *
     * @throws ApiException
     */
    public function deletePublicLink(int $taskId): bool
    {
        $response = $this->client->delete("public-link/task/{$taskId}");

        return $this->parser->parseBoolean($response);
    }

    /**
     * Set total time estimate for task
     *
     * @throws ApiException
     */
    public function setTotalTimeEstimate(int $taskId, int $minutes): bool
    {
        $response = $this->client->post("task/{$taskId}/total-time-estimate", [
            'minutes' => $minutes,
        ]);

        return $this->parser->parseBoolean($response);
    }

    /**
     * Delete total time estimate
     *
     * @throws ApiException
     */
    public function deleteTotalTimeEstimate(int $taskId): bool
    {
        $response = $this->client->delete("task/{$taskId}/total-time-estimate");

        return $this->parser->parseBoolean($response);
    }

    /**
     * Set user time estimate for task
     *
     * @throws ApiException
     */
    public function setUserTimeEstimate(int $taskId, int $userId, int $minutes): bool
    {
        $response = $this->client->post("task/{$taskId}/users-time-estimates/{$userId}", [
            'minutes' => $minutes,
        ]);

        return $this->parser->parseBoolean($response);
    }

    /**
     * Delete user time estimate
     *
     * @throws ApiException
     */
    public function deleteUserTimeEstimate(int $taskId, int $userId): bool
    {
        $response = $this->client->delete("task/{$taskId}/users-time-estimates/{$userId}");

        return $this->parser->parseBoolean($response);
    }

    /**
     * Add comment to task
     *
     * @param string[] $files File UUIDs to attach
     * @throws ApiException
     */
    public function addComment(int $taskId, string $content, array $files = []): Comment
    {
        $data = ['content' => $content];
        if (!empty($files)) {
            $data['files'] = $files;
        }

        $response = $this->client->post("task/{$taskId}/comments", $data);
        $responseData = $this->parser->parseSingle($response);

        return Comment::fromArray($responseData);
    }
}
