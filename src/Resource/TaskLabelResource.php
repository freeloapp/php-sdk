<?php

declare(strict_types=1);

namespace Freelo\Sdk\Resource;

use Freelo\Sdk\Exception\ApiException;

/**
 * Task label resource manager
 *
 * Handles task label operations via the /task-labels/* endpoints.
 */
class TaskLabelResource extends AbstractResource
{
    protected function getEndpoint(): string
    {
        return 'task-labels';
    }

    /**
     * Create task labels
     *
     * @param array<int, array{name: string, color?: string}> $labels Array of label definitions
     * @throws ApiException
     */
    public function create(array $labels): bool
    {
        $response = $this->client->post('task-labels', ['labels' => $labels]);

        return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
    }

    /**
     * Add labels to a task
     *
     * @param array<int, array{name: string, color?: string}> $labels Array of label definitions
     * @throws ApiException
     */
    public function addToTask(int $taskId, array $labels): bool
    {
        $response = $this->client->post(
            "task-labels/add-to-task/{$taskId}",
            ['labels' => $labels]
        );

        return $this->parser->parseBoolean($response);
    }

    /**
     * Remove labels from a task
     *
     * @param array<int, array{name: string, color?: string}> $labels Array of label definitions
     * @throws ApiException
     */
    public function removeFromTask(int $taskId, array $labels): bool
    {
        $response = $this->client->post(
            "task-labels/remove-from-task/{$taskId}",
            ['labels' => $labels]
        );

        return $this->parser->parseBoolean($response);
    }
}
