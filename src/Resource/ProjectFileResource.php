<?php

declare(strict_types=1);

namespace Freelo\Sdk\Resource;

use Freelo\Sdk\Exception\ApiException;
use Freelo\Sdk\Model\File;

/**
 * Project file resource manager
 *
 * Handles files stored in the project Files section. Comment, task and note
 * attachments are uploaded with FileResource::upload() instead.
 */
class ProjectFileResource extends AbstractResource
{
    protected function getEndpoint(): string
    {
        return 'project-files';
    }

    /**
     * Upload a file into the project root or into a directory
     *
     * @param array<string, string> $fields Optional: directory_uuid, caption (defaults to the file name), note, color
     * @throws ApiException
     */
    public function upload(int $projectId, string $filePath, array $fields = []): File
    {
        $response = $this->client->uploadFile('project-files', $filePath, ['project_id' => $projectId] + $fields);

        return File::fromArray($this->parser->parseSingle($response));
    }

    /**
     * Edit the caption, note or colour of a file, or move it to another directory
     *
     * Only the keys present in $data are changed; `note => null` removes the
     * note and `directory_uuid => null` moves the file to the project root.
     *
     * @param array<string, mixed> $data Optional: caption, note, color, directory_uuid
     * @throws ApiException
     */
    public function update(string $fileUuid, array $data): File
    {
        $response = $this->client->patch("project-files/{$fileUuid}", $data);

        return File::fromArray($this->parser->parseSingle($response));
    }

    /**
     * Permanently delete a file of the Files section
     *
     * Attachments of tasks, comments and notes are rejected with 404.
     *
     * @throws ApiException
     */
    public function delete(string $fileUuid): bool
    {
        $response = $this->client->delete("project-files/{$fileUuid}");

        return $this->parser->parseBoolean($response);
    }
}
