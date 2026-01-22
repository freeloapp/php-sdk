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
