<?php

declare(strict_types=1);

namespace Freelo\Sdk\Resource;

use Freelo\Sdk\Exception\ApiException;
use Freelo\Sdk\Http\PaginatedResult;
use Freelo\Sdk\Model\Project;
use Freelo\Sdk\Model\User;

/**
 * Project resource manager
 *
 * Handles all project-related API operations.
 */
class ProjectResource extends AbstractResource
{
    protected function getEndpoint(): string
    {
        return 'projects';
    }

    protected function getSingleEndpoint(): string
    {
        return 'project';
    }

    /**
     * Get all own active projects with tasklists
     *
     * @param array<string, mixed> $filters Available filters:
     *   - order_by: string - name|date_add|date_edited_at (default: name)
     *   - order: string - asc|desc (default: asc)
     * @return Project[]
     * @throws ApiException
     */
    public function list(array $filters = []): array
    {
        $response = $this->client->get($this->getEndpoint(), $filters);
        $data = $this->parser->parseCollection($response);

        return array_map(
            fn(array $item) => Project::fromArray($item),
            $data
        );
    }

    /**
     * Get all projects (owned and invited) - paginated
     *
     * @param array<string, mixed> $filters Available filters:
     *   - order_by: string - name|date_add|date_edited_at (default: date_add)
     *   - order: string - asc|desc (default: asc)
     *   - tags: string[] - Filter by tags, use "without" to get projects without tags
     *   - states_ids: int[] - Project states: 1=active, 2=archived, 3=template
     *   - users_ids: int[] - Filter by project owner IDs
     *   - created_in_range: array{date_from?: string, date_to?: string} - Creation date range (Y-m-d format)
     *   - p: int - Page number (0-based)
     * @return PaginatedResult<Project>
     * @throws ApiException
     */
    public function getAll(array $filters = []): PaginatedResult
    {
        $response = $this->client->get('all-projects', $filters);

        return $this->parser->parsePaginated($response, Project::class);
    }

    /**
     * Get a specific project by ID
     *
     * @throws ApiException
     */
    public function get(int $projectId): Project
    {
        $response = $this->client->get("project/{$projectId}");
        $data = $this->parser->parseSingle($response);

        return Project::fromArray($data);
    }

    /**
     * Get invited projects - paginated
     *
     * @param array<string, mixed> $filters
     * @return PaginatedResult<Project>
     * @throws ApiException
     */
    public function getInvited(array $filters = []): PaginatedResult
    {
        $response = $this->client->get('invited-projects', $filters);

        return $this->parser->parsePaginated($response, Project::class);
    }

    /**
     * Get archived projects - paginated
     *
     * @param array<string, mixed> $filters
     * @return PaginatedResult<Project>
     * @throws ApiException
     */
    public function getArchived(array $filters = []): PaginatedResult
    {
        $response = $this->client->get('archived-projects', $filters);

        return $this->parser->parsePaginated($response, Project::class);
    }

    /**
     * Get template projects - paginated
     *
     * @param array<string, mixed> $filters Available filters:
     *   - order_by: string - name|date_add|date_edited_at (default: date_add)
     *   - order: string - asc|desc (default: asc)
     *   - tags: string[] - Filter by tags
     *   - users_ids: int[] - Filter by project owner IDs
     *   - created_in_range: array{date_from?: string, date_to?: string} - Creation date range (Y-m-d format)
     *   - p: int - Page number (0-based)
     * @return PaginatedResult<Project>
     * @throws ApiException
     */
    public function getTemplates(array $filters = []): PaginatedResult
    {
        $response = $this->client->get('template-projects', $filters);

        return $this->parser->parsePaginated($response, Project::class);
    }

    /**
     * Get user's projects - paginated
     *
     * @param array<string, mixed> $filters Available filters:
     *   - states_ids: int[] - Project states: 1=active, 2=archived, 3=template
     *   - order_by: string - name|date_add|date_edited_at (default: date_add)
     *   - order: string - asc|desc (default: desc)
     *   - p: int - Page number (0-based)
     * @return PaginatedResult<Project>
     * @throws ApiException
     */
    public function getUserProjects(int $userId, array $filters = []): PaginatedResult
    {
        $response = $this->client->get("user/{$userId}/all-projects", $filters);

        return $this->parser->parsePaginated($response, Project::class);
    }

    /**
     * Create a new project
     *
     * @param array<string, mixed> $data Required: name, currency_iso. Optional: project_owner_id
     * @throws ApiException
     */
    public function create(array $data): Project
    {
        $response = $this->client->post($this->getEndpoint(), $data);
        $responseData = $this->parser->parseSingle($response);

        return Project::fromArray($responseData);
    }

    /**
     * Update a project (partial update)
     *
     * Only the keys present in $data are changed; omitted fields are left untouched.
     * Name and deadline are the only editable fields - owner and currency must be
     * changed in the Freelo app.
     *
     * @param array<string, mixed> $data Available fields:
     *   - name: string - New project name
     *   - due_date: string|null - Deadline as Y-m-d; null clears the deadline
     * @throws ApiException
     */
    public function update(int $projectId, array $data): Project
    {
        $response = $this->client->patch("project/{$projectId}", $data);
        $responseData = $this->parser->parseSingle($response);

        return Project::fromArray($responseData);
    }

    /**
     * Delete a project
     *
     * @throws ApiException
     */
    public function delete(int $projectId): bool
    {
        $response = $this->client->delete("project/{$projectId}");

        return $this->parser->parseBoolean($response);
    }

    /**
     * Archive a project
     *
     * @throws ApiException
     */
    public function archive(int $projectId): bool
    {
        $response = $this->client->post("project/{$projectId}/archive");

        return $this->parser->parseBoolean($response);
    }

    /**
     * Activate an archived project
     *
     * @throws ApiException
     */
    public function activate(int $projectId): bool
    {
        $response = $this->client->post("project/{$projectId}/activate");

        return $this->parser->parseBoolean($response);
    }

    /**
     * Create project from template
     *
     * @param array<string, mixed> $data
     * @throws ApiException
     */
    public function createFromTemplate(int $templateId, array $data = []): Project
    {
        $response = $this->client->post("project/create-from-template/{$templateId}", $data);
        $responseData = $this->parser->parseSingle($response);

        return Project::fromArray($responseData);
    }

    /**
     * Get project workers - paginated
     *
     * @param array<string, mixed> $filters
     * @return PaginatedResult<User>
     * @throws ApiException
     */
    public function getWorkers(int $projectId, array $filters = []): PaginatedResult
    {
        $response = $this->client->get("project/{$projectId}/workers", $filters);

        return $this->parser->parsePaginated($response, User::class);
    }

    /**
     * Remove workers from project by IDs
     *
     * @param int[] $userIds
     * @throws ApiException
     */
    public function removeWorkersByIds(int $projectId, array $userIds): bool
    {
        $response = $this->client->post(
            "project/{$projectId}/remove-workers/by-ids",
            ['users_ids' => $userIds]
        );

        return $this->parser->parseBoolean($response);
    }

    /**
     * Remove workers from project by emails
     *
     * @param string[] $emails
     * @throws ApiException
     */
    public function removeWorkersByEmails(int $projectId, array $emails): bool
    {
        $response = $this->client->post(
            "project/{$projectId}/remove-workers/by-emails",
            ['users_emails' => $emails]
        );

        return $this->parser->parseBoolean($response);
    }

    /**
     * Get the current budget state of a project
     *
     * Only the project owner and project commanders may read the budget.
     *
     * @return array<string, mixed> Budget settings plus consumption and remaining
     *   values for both money (budget, spent_cost, remaining_cost) and time
     *   (minutes_budget, spent_minutes, remaining_minutes).
     * @throws ApiException
     */
    public function getBudget(int $projectId): array
    {
        $response = $this->client->get("project/{$projectId}/budget");

        return $this->parser->parseSingle($response);
    }

    /**
     * Set or change the project budget
     *
     * To cancel the budget send budget: null and minutes_budget: 0 with
     * is_recurrent: false - the budget resets to empty/zero.
     *
     * @param array<string, mixed> $data Available fields:
     *   - is_recurrent: bool - Whether the budget resets periodically (required)
     *   - budget: string|null - Amount in minor currency units as string
     *     (e.g. "100000" for 1000.00); null clears the money budget
     *   - minutes_budget: int|null - Time budget in minutes; 0 clears it
     *   - null_in_day_of_month: int|null - Day of month the recurrent budget resets on
     *   - null_after_months_count: int|null - Reset interval in months
     * @return array<string, mixed> Budget state after the change
     * @throws ApiException
     */
    public function setBudget(int $projectId, array $data): array
    {
        $response = $this->client->post("project/{$projectId}/budget", $data);

        return $this->parser->parseSingle($response);
    }

    /**
     * Reset the project budget now
     *
     * Snapshots the current period into budget history and zeroes consumption.
     * When nothing has been consumed the reset is a no-op and the current state
     * is returned unchanged.
     *
     * @return array<string, mixed> Budget state after the reset
     * @throws ApiException
     */
    public function resetBudget(int $projectId): array
    {
        $response = $this->client->post("project/{$projectId}/budget/reset");

        return $this->parser->parseSingle($response);
    }
}
