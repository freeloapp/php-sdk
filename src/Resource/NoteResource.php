<?php

declare(strict_types=1);

namespace Freelo\Sdk\Resource;

use Freelo\Sdk\Exception\ApiException;
use Freelo\Sdk\Model\Note;

/**
 * Note resource manager
 *
 * Handles all note-related API operations.
 */
class NoteResource extends AbstractResource
{
    protected function getEndpoint(): string
    {
        return 'notes';
    }

    protected function getSingleEndpoint(): string
    {
        return 'note';
    }

    /**
     * Create a note in a project
     *
     * @throws ApiException
     */
    public function create(int $projectId, string $name, ?string $content = null): Note
    {
        $data = ['name' => $name];
        if ($content !== null) {
            $data['content'] = $content;
        }

        $response = $this->client->post("project/{$projectId}/note", $data);
        $responseData = $this->parser->parseSingle($response);

        return Note::fromArray($responseData);
    }

    /**
     * Get a note by ID
     *
     * @throws ApiException
     */
    public function get(int $noteId): Note
    {
        $response = $this->client->get("note/{$noteId}");
        $data = $this->parser->parseSingle($response);

        return Note::fromArray($data);
    }

    /**
     * Update a note
     *
     * @throws ApiException
     */
    public function update(int $noteId, string $name, ?string $content = null): Note
    {
        $data = ['name' => $name];
        if ($content !== null) {
            $data['content'] = $content;
        }

        $response = $this->client->post("note/{$noteId}", $data);
        $responseData = $this->parser->parseSingle($response);

        return Note::fromArray($responseData);
    }

    /**
     * Delete a note
     *
     * @throws ApiException
     */
    public function delete(int $noteId): Note
    {
        $response = $this->client->delete("note/{$noteId}");
        $data = $this->parser->parseSingle($response);

        return Note::fromArray($data);
    }
}
