<?php

declare(strict_types=1);

namespace Freelo\Sdk\Resource;

use Freelo\Sdk\Exception\ApiException;
use Freelo\Sdk\Http\PaginatedResult;
use Freelo\Sdk\Model\User;

/**
 * User resource manager
 *
 * Handles all user-related API operations.
 */
class UserResource extends AbstractResource
{
    protected function getEndpoint(): string
    {
        return 'users';
    }

    protected function getSingleEndpoint(): string
    {
        return 'user';
    }

    /**
     * Get all users (coworkers) - paginated
     *
     * @param array<string, mixed> $filters
     * @return PaginatedResult<User>
     * @throws ApiException
     */
    public function list(array $filters = []): PaginatedResult
    {
        $response = $this->client->get('users', $filters);

        return $this->parser->parsePaginated($response, User::class);
    }

    /**
     * Find users who promoted me as project manager
     *
     * @return User[]
     * @throws ApiException
     */
    public function getProjectManagerOf(): array
    {
        $response = $this->client->get('users/project-manager-of');
        $data = $this->parser->parseCollection($response);

        return array_map(fn(array $item) => User::fromArray($item), $data);
    }

    /**
     * Invite users to projects
     *
     * @param int[] $projectIds
     * @param string[] $emails
     * @param int[] $userIds
     * @return array<string, mixed>
     * @throws ApiException
     */
    public function inviteToProjects(array $projectIds, array $emails = [], array $userIds = []): array
    {
        $response = $this->client->post('users/manage-workers', [
            'projects_ids' => $projectIds,
            'emails' => $emails,
            'users_ids' => $userIds,
        ]);

        return $this->parser->parseSingle($response);
    }

    /**
     * Get user's out of office status
     *
     * @return array<string, mixed>|null
     * @throws ApiException
     */
    public function getOutOfOffice(int $userId): ?array
    {
        $response = $this->client->get("user/{$userId}/out-of-office");
        $data = $this->parser->parseSingle($response);

        return $data['out_of_office'] ?? null;
    }

    /**
     * Enable out of office for user
     *
     * @throws ApiException
     */
    public function enableOutOfOffice(int $userId, string $dateFrom, string $dateTo): bool
    {
        $response = $this->client->post("user/{$userId}/out-of-office", [
            'out_of_office' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ]);

        return $this->parser->parseBoolean($response);
    }

    /**
     * Disable out of office for user
     *
     * @throws ApiException
     */
    public function disableOutOfOffice(int $userId): bool
    {
        $response = $this->client->delete("user/{$userId}/out-of-office");

        return $this->parser->parseBoolean($response);
    }
}
