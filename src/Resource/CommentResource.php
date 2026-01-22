<?php

declare(strict_types=1);

namespace Freelo\Sdk\Resource;

use Freelo\Sdk\Exception\ApiException;
use Freelo\Sdk\Http\PaginatedResult;
use Freelo\Sdk\Model\Comment;

/**
 * Comment resource manager
 *
 * Handles all comment-related API operations.
 */
class CommentResource extends AbstractResource
{
    protected function getEndpoint(): string
    {
        return 'comments';
    }

    protected function getSingleEndpoint(): string
    {
        return 'comment';
    }

    /**
     * Get all comments - paginated
     *
     * @param array<string, mixed> $filters
     * @return PaginatedResult<Comment>
     * @throws ApiException
     */
    public function getAll(array $filters = []): PaginatedResult
    {
        $response = $this->client->get('all-comments', $filters);

        return $this->parser->parsePaginated($response, Comment::class);
    }

    /**
     * Edit a comment
     *
     * @param string[] $files File UUIDs to attach
     * @throws ApiException
     */
    public function update(int $commentId, string $content, array $files = []): Comment
    {
        $data = ['content' => $content];
        if (!empty($files)) {
            $data['files'] = $files;
        }

        $response = $this->client->post("comment/{$commentId}", $data);
        $responseData = $this->parser->parseSingle($response);

        return Comment::fromArray($responseData);
    }
}
