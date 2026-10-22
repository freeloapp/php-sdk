<?php

declare(strict_types=1);

namespace Freelo\Sdk\Resource;

use Freelo\Sdk\Exception\ApiException;
use Freelo\Sdk\Model\File;

/**
 * Directory resource manager
 *
 * Handles directories of the project Files section. List their content with
 * FileResource::getAll() and the `directory_uuid` filter.
 */
class DirectoryResource extends AbstractResource
{
    protected function getEndpoint(): string
    {
        return 'directories';
    }

    /**
     * Create a directory in the project root or inside another directory
     *
     * @param array<string, mixed> $data Required: name. Optional: color, directory_uuid (parent)
     * @throws ApiException
     */
    public function create(int $projectId, array $data): File
    {
        $response = $this->client->post('directories', ['project_id' => $projectId] + $data);

        return File::fromArray($this->parser->parseSingle($response));
    }

    /**
     * Get a directory including `items_count` (number of its direct items)
     *
     * @throws ApiException
     */
    public function get(string $directoryUuid): File
    {
        $response = $this->client->get("directories/{$directoryUuid}");

        return File::fromArray($this->parser->parseSingle($response));
    }

    /**
     * Rename, recolor or move a directory
     *
     * Only the keys present in $data are changed; `directory_uuid => null`
     * moves the directory to the project root.
     *
     * @param array<string, mixed> $data Optional: name, color, directory_uuid
     * @throws ApiException
     */
    public function update(string $directoryUuid, array $data): File
    {
        $response = $this->client->patch("directories/{$directoryUuid}", $data);

        return File::fromArray($this->parser->parseSingle($response));
    }

    /**
     * Permanently delete a directory
     *
     * A non-empty directory is rejected with 409 unless $force is true, in which
     * case its whole content goes with it. This cannot be undone.
     *
     * @throws ApiException
     */
    public function delete(string $directoryUuid, bool $force = false): bool
    {
        $response = $this->client->delete(
            "directories/{$directoryUuid}",
            $force ? ['force' => 'true'] : [],
        );

        return $this->parser->parseBoolean($response);
    }
}
