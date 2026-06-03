<?php

declare(strict_types=1);

namespace Freelo\Sdk\Resource;

use Freelo\Sdk\Exception\ApiException;
use Freelo\Sdk\Http\PaginatedResult;
use Freelo\Sdk\Model\Tasklist;
use Freelo\Sdk\Model\User;

/**
 * Tasklist resource manager
 *
 * Handles all tasklist-related API operations.
 */
class TasklistResource extends AbstractResource
{
    protected function getEndpoint(): string
    {
        return 'tasklists';
    }

    protected function getSingleEndpoint(): string
    {
        return 'tasklist';
    }

    /**
     * Get all tasklists - paginated
     *
     * @param array<string, mixed> $filters
     * @return PaginatedResult<Tasklist>
     * @throws ApiException
     */
    public function getAll(array $filters = []): PaginatedResult
    {
        $response = $this->client->get('all-tasklists', $filters);

        return $this->parser->parsePaginated($response, Tasklist::class);
    }

    /**
     * List tasklists in a project
     *
     * @return Tasklist[]
     * @throws ApiException
     */
    public function list(int $projectId): array
    {
        $response = $this->client->get("project/{$projectId}/tasklists");
        $data = $this->parser->parseCollection($response);

        return array_map(
            fn(array $item) => Tasklist::fromArray($item),
            $data
        );
    }

    /**
     * Get a specific tasklist by ID
     *
     * @throws ApiException
     */
    public function get(int $tasklistId): Tasklist
    {
        $response = $this->client->get("tasklist/{$tasklistId}");
        $data = $this->parser->parseSingle($response);

        return Tasklist::fromArray($data);
    }

    /**
     * Create a new tasklist in a project
     *
     * @param array<string, mixed> $data
     * @throws ApiException
     */
    public function create(int $projectId, array $data): Tasklist
    {
        $response = $this->client->post("project/{$projectId}/tasklists", $data);
        $responseData = $this->parser->parseSingle($response);

        return Tasklist::fromArray($responseData);
    }

    /**
     * Edit a tasklist
     *
     * All fields are optional - only keys present in $data are applied.
     *
     * @param array<string, mixed> $data Available fields:
     *   - name: string - New tasklist name
     *   - budget: string|null - Amount in minor currency units as string
     *     (e.g. "100000" for 1000.00); null or "0" clears
     *   - time_budget_minutes: int|null - Time fund in minutes (>= 0); null clears
     *   - priority: int - New position within the project (1 = first); positional ordering, not task priority
     *   - tracking_users_ids: int[] - Followers; [] clears all
     *   - should_change_existing_tasks: bool - Propagate follower change to existing tasks
     *   - worker_id: int|null - Default worker; null clears
     * @return bool True when the priority change was applied (or not requested).
     *   False means the other fields committed but the priority renumber failed -
     *   retry the priority field alone.
     * @throws ApiException
     */
    public function edit(int $tasklistId, array $data): bool
    {
        $response = $this->client->post("tasklist/{$tasklistId}/edit", $data);
        $responseData = $this->parser->parseSingle($response);

        return (bool) ($responseData['priorityApplied'] ?? true);
    }

    /**
     * Create tasklist from template
     *
     * @param array<string, mixed> $data
     * @throws ApiException
     */
    public function createFromTemplate(int $templateId, array $data): Tasklist
    {
        $response = $this->client->post("tasklist/create-from-template/{$templateId}", $data);
        $responseData = $this->parser->parseSingle($response);

        return Tasklist::fromArray($responseData);
    }

    /**
     * Get assignable workers for a tasklist
     *
     * @return User[]
     * @throws ApiException
     */
    public function getAssignableWorkers(int $projectId, int $tasklistId): array
    {
        $response = $this->client->get(
            "project/{$projectId}/tasklist/{$tasklistId}/assignable-workers"
        );
        $data = $this->parser->parseCollection($response);

        return array_map(
            fn(array $item) => User::fromArray($item),
            $data
        );
    }
}
