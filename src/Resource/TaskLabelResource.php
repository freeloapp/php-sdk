<?php

declare(strict_types=1);

namespace Freelo\Sdk\Resource;

use Freelo\Sdk\Exception\ApiException;
use Freelo\Sdk\Model\TaskLabel;
use Freelo\Sdk\Model\TaskLabelColor;

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
     * Find all task labels usable by the authenticated user
     *
     * Returns labels attached to tasks across the caller's owned and invited
     * projects (active, archived or template). Sorted by name ascending.
     *
     * @return TaskLabel[]
     * @throws ApiException
     */
    public function findAvailable(): array
    {
        $response = $this->client->get('task-labels/find-available');
        $data = $this->parser->parseSingle($response);

        return array_map(
            fn(array $item) => TaskLabel::fromArray($item),
            $data['labels'] ?? []
        );
    }

    /**
     * List the accepted task-label colors
     *
     * Returns the fixed palette of colors accepted when creating or assigning
     * task labels. Send the `color` (hex) value back on create/add-to-task;
     * `displayName` is display-only and is not accepted as input. Exactly one
     * color is marked `isDefault` (applied when a label is created without one).
     *
     * @return TaskLabelColor[]
     * @throws ApiException
     */
    public function getColors(): array
    {
        $response = $this->client->get('task-label-colors');
        $data = $this->parser->parseSingle($response);

        return array_map(
            fn(array $item) => TaskLabelColor::fromArray($item),
            $data['colors'] ?? []
        );
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
     * Merge task labels into a single target label
     *
     * Every task carrying one of the source labels ends up carrying the target
     * label instead; the source labels are detached from those tasks (their
     * definitions are not deleted). The target label's name and color are taken
     * from the existing target label — they are not sent by the client.
     *
     * Both the target and every source label must be owned by the caller,
     * otherwise the API responds 404. The replacement is applied only to tasks
     * in projects where the caller is a commander.
     *
     * @param string[] $fromUuids UUIDs of the labels to merge away
     * @param string $toUuid UUID of the label the source labels are merged into
     * @throws ApiException
     */
    public function merge(array $fromUuids, string $toUuid): bool
    {
        $response = $this->client->post('task-labels/merge', [
            'from_uuids' => array_values($fromUuids),
            'to_uuid' => $toUuid,
        ]);

        return $this->parser->parseBoolean($response);
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
