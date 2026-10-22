<?php

declare(strict_types=1);

namespace Freelo\Sdk\Resource;

use Freelo\Sdk\Exception\ApiException;
use Freelo\Sdk\Model\File;

/**
 * Link resource manager
 *
 * Handles links to external documents stored in the project Files section.
 * `link_type` is derived from the URL server-side and must not be sent.
 */
class LinkResource extends AbstractResource
{
    protected function getEndpoint(): string
    {
        return 'links';
    }

    /**
     * Add a link to the project root or into a directory
     *
     * @param array<string, mixed> $data Required: name, link. Optional: note, color, directory_uuid
     * @throws ApiException
     */
    public function create(int $projectId, array $data): File
    {
        $response = $this->client->post('links', ['project_id' => $projectId] + $data);

        return File::fromArray($this->parser->parseSingle($response));
    }

    /**
     * Edit or move a link
     *
     * Only the keys present in $data are changed; `note => null` removes the
     * note and `directory_uuid => null` moves the link to the project root.
     *
     * @param array<string, mixed> $data Optional: name, link, note, color, directory_uuid
     * @throws ApiException
     */
    public function update(string $linkUuid, array $data): File
    {
        $response = $this->client->patch("links/{$linkUuid}", $data);

        return File::fromArray($this->parser->parseSingle($response));
    }

    /**
     * Delete a link (soft-delete, not restorable via the API)
     *
     * @throws ApiException
     */
    public function delete(string $linkUuid): bool
    {
        $response = $this->client->delete("links/{$linkUuid}");

        return $this->parser->parseBoolean($response);
    }
}
