<?php

declare(strict_types=1);

namespace Freelo\Sdk\Resource;

use Freelo\Sdk\Exception\ApiException;
use Freelo\Sdk\Model\ProjectLabel;

/**
 * Project label resource manager
 *
 * Handles all project label-related API operations.
 */
class ProjectLabelResource extends AbstractResource
{
    protected function getEndpoint(): string
    {
        return 'project-labels';
    }

    /**
     * Find all available project labels
     *
     * @return ProjectLabel[]
     * @throws ApiException
     */
    public function findAvailable(): array
    {
        $response = $this->client->get('project-labels/find-available');
        $data = $this->parser->parseSingle($response);

        // API returns 'labels'; older versions used 'label'
        return array_map(
            fn(array $item) => ProjectLabel::fromArray($item),
            $data['labels'] ?? $data['label'] ?? []
        );
    }

    /**
     * Edit a project label
     *
     * @param array<string, mixed> $data
     * @throws ApiException
     */
    public function update(int $labelId, array $data): bool
    {
        $response = $this->client->post("project-labels/{$labelId}", $data);

        return $this->parser->parseBoolean($response);
    }

    /**
     * Delete a project label
     *
     * @throws ApiException
     */
    public function delete(int $labelId): bool
    {
        $response = $this->client->delete("project-labels/{$labelId}");

        return $this->parser->parseBoolean($response);
    }

    /**
     * Add label to a project
     *
     * @param array<string, mixed> $labelData
     * @throws ApiException
     */
    public function addToProject(int $projectId, array $labelData): bool
    {
        $response = $this->client->post(
            "project-labels/add-to-project/{$projectId}",
            $labelData
        );

        return $this->parser->parseBoolean($response);
    }

    /**
     * Remove label from a project
     *
     * @param array<string, mixed> $labelData
     * @throws ApiException
     */
    public function removeFromProject(int $projectId, array $labelData): bool
    {
        $response = $this->client->post(
            "project-labels/remove-from-project/{$projectId}",
            $labelData
        );

        return $this->parser->parseBoolean($response);
    }
}
