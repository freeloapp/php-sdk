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
     * @param array<string, mixed> $filters
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
