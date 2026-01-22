<?php

declare(strict_types=1);

namespace Freelo\Sdk\Resource;

use Freelo\Sdk\Exception\ApiException;
use Freelo\Sdk\Http\PaginatedResult;
use Freelo\Sdk\Model\File;

/**
 * File resource manager
 *
 * Handles all file-related API operations including upload and download.
 */
class FileResource extends AbstractResource
{
    protected function getEndpoint(): string
    {
        return 'file';
    }

    /**
     * Get all docs and files - paginated
     *
     * @param array<string, mixed> $filters
     * @return PaginatedResult<File>
     * @throws ApiException
     */
    public function getAll(array $filters = []): PaginatedResult
    {
        $response = $this->client->get('all-docs-and-files', $filters);

        return $this->parser->parsePaginated($response, File::class);
    }

    /**
     * Download a file by UUID
     *
     * @throws ApiException
     */
    public function download(string $fileUuid): string
    {
        $response = $this->client->get("file/{$fileUuid}");

        return $response->getBody();
    }

    /**
     * Upload a file
     * Returns UUID that can be used in comments
     *
     * @throws ApiException
     */
    public function upload(string $filePath): string
    {
        $response = $this->client->uploadFile('file/upload', $filePath);
        $data = $this->parser->parseSingle($response);

        return $data['uuid'] ?? '';
    }
}
