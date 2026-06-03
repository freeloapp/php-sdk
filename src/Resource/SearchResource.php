<?php

declare(strict_types=1);

namespace Freelo\Sdk\Resource;

use Freelo\Sdk\Exception\ApiException;
use Freelo\Sdk\Http\PaginatedResult;
use Freelo\Sdk\Model\SearchResult;

/**
 * Search resource manager
 *
 * Handles search API operations using Elasticsearch.
 */
class SearchResource extends AbstractResource
{
    protected function getEndpoint(): string
    {
        return 'search';
    }

    /**
     * Search across Freelo
     *
     * @param string $query Fulltext query (2-200 chars after trim). Double-quoted
     *   fragments match as ordered exact phrases.
     * @param array<string, mixed> $filters Available filters:
     *   - entity_type: string - task|subtask|taskcheck|project|tasklist|file|comment
     *   - exclude_entity_types: string[] - Exclude categories (same values as entity_type)
     *   - state_ids: string[] - active|archived|finished|template|not_template|
     *     archived_finished|archived_unfinished (default ["active"])
     *   - projects_ids: int[] - Restrict to these projects
     *   - tasklists_ids: int[] - Restrict to these tasklists
     *   - tasks_ids: int[] - Restrict to these tasks (e.g. their comments/files)
     *   - authors_ids: int[] - Filter by author (entity owner)
     *   - workers_ids: int[] - Filter by assignee (tasks/subtasks)
     *   - is_subtask: bool - Restrict to smart-subtask documents
     *   - due_date: array{date_from?: string, date_to?: string} - Due date range (Y-m-d)
     *   - sort: array{order_by: string, order: string} - order_by: last_updated; order: ASC|DESC (omit for relevance)
     *   - lang: string - cs_cz|en_us (analyzer/stemmer language)
     *   - page: int - Zero-based page index
     *   - limit: int - Page size (default 100)
     * @return PaginatedResult<SearchResult>
     * @throws ApiException
     */
    public function search(string $query, array $filters = []): PaginatedResult
    {
        $data = array_merge(['search_query' => $query], $filters);

        $response = $this->client->post('search', $data);

        return $this->parser->parsePaginated($response, SearchResult::class);
    }
}
