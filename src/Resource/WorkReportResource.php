<?php

declare(strict_types=1);

namespace Freelo\Sdk\Resource;

use Freelo\Sdk\Exception\ApiException;
use Freelo\Sdk\Http\PaginatedResult;
use Freelo\Sdk\Model\WorkReport;

/**
 * Work report resource manager
 *
 * Handles all work report-related API operations.
 */
class WorkReportResource extends AbstractResource
{
    protected function getEndpoint(): string
    {
        return 'work-reports';
    }

    /**
     * Get work reports - paginated
     *
     * @param array<string, mixed> $filters Available filters:
     *   - projects_ids: int[] - Filter by project IDs
     *   - users_ids: int[] - Filter by user IDs
     *   - tasks_ids: int[] - Filter by task IDs
     *   - tasks_labels: string[] - Filter by task label UUIDs
     *   - date_reported_range: array{date_from?: string, date_to?: string} - Date reported range (Y-m-d format)
     *   - date_add_range: array{date_from?: string, date_to?: string} - Date added range (Y-m-d format)
     *   - date_edited_from: string - Filter reports edited from this date (Y-m-d format)
     *   - p: int - Page number (0-based)
     * @return PaginatedResult<WorkReport>
     * @throws ApiException
     */
    public function list(array $filters = []): PaginatedResult
    {
        $response = $this->client->get('work-reports', $filters);

        return $this->parser->parsePaginated($response, WorkReport::class);
    }

    /**
     * Create a work report for a task
     *
     * @param array<string, mixed> $data
     * @throws ApiException
     */
    public function create(int $taskId, array $data): WorkReport
    {
        $response = $this->client->post("task/{$taskId}/work-reports", $data);
        $responseData = $this->parser->parseSingle($response);

        return WorkReport::fromArray($responseData);
    }

    /**
     * Update a work report
     *
     * @param array<string, mixed> $data
     * @throws ApiException
     */
    public function update(int $workReportId, array $data): WorkReport
    {
        $response = $this->client->post("work-reports/{$workReportId}", $data);
        $responseData = $this->parser->parseSingle($response);

        return WorkReport::fromArray($responseData);
    }

    /**
     * Delete a work report
     *
     * @throws ApiException
     */
    public function delete(int $workReportId): bool
    {
        $response = $this->client->delete("work-reports/{$workReportId}");

        return $this->parser->parseBoolean($response);
    }
}
