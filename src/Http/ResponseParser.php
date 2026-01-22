<?php

declare(strict_types=1);

namespace Freelo\Sdk\Http;

use Freelo\Sdk\Exception\ApiException;

/**
 * Parses API responses and converts them to domain objects
 *
 * @phpstan-type ModelClass class-string<object>
 *
 * Handles common response patterns including:
 * - Single item responses
 * - Collection responses
 * - Paginated responses (with nested data structure)
 * - Error responses
 */
class ResponseParser
{
    /**
     * Parse a single item from response
     *
     * @param Response $response
     * @return array<string, mixed>
     * @throws ApiException
     */
    public function parseSingle(Response $response): array
    {
        $response->throwIfError();

        try {
            $data = $response->json();

            // Handle wrapped responses (e.g., {"data": {...}})
            if (isset($data['data']) && is_array($data['data'])) {
                // Check if data contains a single nested object (e.g., {"data": {"project": {...}}})
                foreach ($data['data'] as $key => $value) {
                    if (is_array($value) && !$this->isListArray($value)) {
                        return $value;
                    }
                }
                return $data['data'];
            }

            return $data;
        } catch (\JsonException $e) {
            throw new ApiException('Failed to parse JSON response: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Parse a collection of items from response
     *
     * @param Response $response
     * @param string|null $dataKey Optional key to extract items from data object
     * @return array<int, array<string, mixed>>
     * @throws ApiException
     */
    public function parseCollection(Response $response, ?string $dataKey = null): array
    {
        $response->throwIfError();

        try {
            $data = $response->json();

            // Handle wrapped responses (e.g., {"data": {"projects": [...]}})
            if (isset($data['data']) && is_array($data['data'])) {
                // If dataKey is specified, extract from that key
                if ($dataKey !== null && isset($data['data'][$dataKey])) {
                    return array_values($data['data'][$dataKey]);
                }

                // Try to find the first array in data
                foreach ($data['data'] as $value) {
                    if (is_array($value) && $this->isListArray($value)) {
                        return array_values($value);
                    }
                }

                // Direct array in data
                if ($this->isArrayOfArrays($data['data'])) {
                    return array_values($data['data']);
                }
            }

            // If the response is an array of arrays, return it
            if ($this->isArrayOfArrays($data)) {
                return array_values($data);
            }

            // Otherwise, wrap it in an array
            return [$data];
        } catch (\JsonException $e) {
            throw new ApiException('Failed to parse JSON response: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Parse a paginated response into raw data
     *
     * Handles Freelo API format:
     * {
     *     "total": 150,
     *     "count": 20,
     *     "page": 0,
     *     "per_page": 20,
     *     "data": { "projects": [...] }
     * }
     *
     * @param Response $response
     * @param string|null $dataKey Key to extract items from data object (e.g., 'projects')
     * @return array{data: array<int, array<string, mixed>>, pagination: array<string, mixed>}
     * @throws ApiException
     */
    public function parsePaginatedRaw(Response $response, ?string $dataKey = null): array
    {
        $response->throwIfError();

        try {
            $json = $response->json();

            // Extract pagination info (Freelo API format)
            $total = $json['total'] ?? 0;
            $count = $json['count'] ?? 0;
            $page = $json['page'] ?? 0;
            $perPage = $json['per_page'] ?? 20;

            // Extract items from nested data structure
            $data = $json['data'] ?? [];
            $rawItems = [];

            if (is_array($data)) {
                if ($dataKey !== null && isset($data[$dataKey])) {
                    // Extract from specified key (e.g., data.projects)
                    $rawItems = $data[$dataKey];
                } else {
                    // Find the first array value in data
                    foreach ($data as $value) {
                        if (is_array($value) && $this->isListArray($value)) {
                            $rawItems = $value;
                            break;
                        }
                    }

                    // If data is directly an array of items
                    if (empty($rawItems) && $this->isListArray($data)) {
                        $rawItems = $data;
                    }
                }
            }

            // Update count if not provided
            if ($count === 0 && !empty($rawItems)) {
                $count = count($rawItems);
            }

            return [
                'data' => is_array($rawItems) ? $rawItems : [],
                'pagination' => [
                    'total' => $total,
                    'count' => $count,
                    'page' => $page,
                    'per_page' => $perPage,
                ],
            ];
        } catch (\JsonException $e) {
            throw new ApiException('Failed to parse JSON response: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Parse a paginated response into a PaginatedResult with model instances
     *
     * @template T of object
     * @param Response $response
     * @param class-string<T> $modelClass The model class with a fromArray() method
     * @param string|null $dataKey Key to extract items from data object (e.g., 'projects')
     * @return PaginatedResult<T>
     * @throws ApiException
     *
     * @phpstan-param class-string<T> $modelClass
     * @phpstan-return PaginatedResult<T>
     */
    public function parsePaginated(Response $response, string $modelClass, ?string $dataKey = null): PaginatedResult
    {
        $raw = $this->parsePaginatedRaw($response, $dataKey);

        /** @var T[] $items */
        $items = array_map(
            /** @phpstan-ignore-next-line */
            fn(array $item) => $modelClass::fromArray($item),
            $raw['data']
        );

        return new PaginatedResult(
            items: $items,
            total: $raw['pagination']['total'],
            count: $raw['pagination']['count'],
            page: $raw['pagination']['page'],
            perPage: $raw['pagination']['per_page'],
        );
    }

    /**
     * Parse a boolean response (e.g., for delete operations)
     *
     * @param Response $response
     * @return bool
     * @throws ApiException
     */
    public function parseBoolean(Response $response): bool
    {
        $response->throwIfError();

        try {
            $json = $response->json();

            // Check for explicit success result
            if (isset($json['result']) && $json['result'] === 'success') {
                return true;
            }
        } catch (\JsonException) {
            // Ignore JSON parsing errors for boolean responses
        }

        // 2xx status codes indicate success
        return $response->isSuccessful();
    }

    /**
     * Check if an array is an array of arrays (collection)
     *
     * @param mixed $data
     * @return bool
     */
    private function isArrayOfArrays(mixed $data): bool
    {
        if (!is_array($data)) {
            return false;
        }

        if (empty($data)) {
            return true;
        }

        // Check if all elements are arrays
        foreach ($data as $item) {
            if (!is_array($item)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if an array is a list (sequential integer keys starting from 0)
     *
     * @param mixed $data
     * @return bool
     */
    private function isListArray(mixed $data): bool
    {
        if (!is_array($data)) {
            return false;
        }

        if (empty($data)) {
            return true;
        }

        return array_is_list($data);
    }
}
