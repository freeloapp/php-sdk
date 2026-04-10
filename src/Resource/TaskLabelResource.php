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
     * Each label supports two input modes:
     * (1) UUID only — assigns an existing label by UUID.
     * (2) Name-based — name (required), optionally color and uuid.
     *
     * @param array<int, array{uuid?: string, name?: string, color?: string}> $labels
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
     * Each label supports three input modes:
     * (1) UUID — removes the label identified by UUID.
     * (2) Name only — removes all labels with that name.
     * (3) Name + color — removes the label matching both.
     *
     * @param array<int, array{uuid?: string, name?: string, color?: string}> $labels
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
