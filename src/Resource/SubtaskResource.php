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
}
