<?php

declare(strict_types=1);

namespace Freelo\Sdk\Resource;

use Freelo\Sdk\Http\FreeloClient;
use Freelo\Sdk\Http\ResponseParser;

/**
 * Base class for all resource managers
 *
 * Provides common functionality for interacting with API resources.
 */
abstract class AbstractResource
{
    protected readonly ResponseParser $parser;

    public function __construct(
        protected readonly FreeloClient $client,
    ) {
        $this->parser = $client->getResponseParser();
    }

    /**
     * Get the base endpoint for this resource (plural form, used for list/create)
     */
    abstract protected function getEndpoint(): string;

    /**
     * Get the singular endpoint for this resource (used for get/update/delete single item)
     *
     * Override this method if the API uses a different pattern for single resources.
     * By default, returns the same as getEndpoint().
     */
    protected function getSingleEndpoint(): string
    {
        return $this->getEndpoint();
    }

    /**
     * Build a full endpoint path using the plural endpoint
     */
    protected function buildEndpoint(string $path = ''): string
    {
        $base = rtrim($this->getEndpoint(), '/');

        if ($path === '') {
            return $base;
        }

        return $base . '/' . ltrim($path, '/');
    }

    /**
     * Build a full endpoint path using the singular endpoint (for single resource operations)
     */
    protected function buildSingleEndpoint(string $id, string $path = ''): string
    {
        $base = rtrim($this->getSingleEndpoint(), '/') . '/' . $id;

        if ($path === '') {
            return $base;
        }

        return $base . '/' . ltrim($path, '/');
    }

    /**
     * Prepare filter parameters for API request
     *
     * Handles conversion of array parameters to the correct format (key[] syntax)
     * expected by the Freelo API.
     *
     * @param array<string, mixed> $filters Raw filter parameters
     * @return array<string, mixed> Prepared parameters ready for HTTP request
     */
    protected function prepareFilters(array $filters): array
    {
        $params = [];

        foreach ($filters as $key => $value) {
            if ($value === null) {
                continue;
            }

            // Handle nested arrays (e.g., created_in_range.date_from)
            if (is_array($value) && !$this->isListArray($value)) {
                foreach ($value as $subKey => $subValue) {
                    $params[$key . '[' . $subKey . ']'] = $subValue;
                }
                continue;
            }

            // Handle array parameters (convert to key[] format for list arrays)
            if (is_array($value) && $this->isListArray($value) && !str_ends_with($key, '[]')) {
                $params[$key . '[]'] = $value;
            } else {
                $params[$key] = $value;
            }
        }

        return $params;
    }

    /**
     * Check if an array is a list (sequential integer keys starting from 0)
     *
     * @param array<mixed> $array
     */
    private function isListArray(array $array): bool
    {
        if (empty($array)) {
            return true;
        }

        return array_is_list($array);
    }
}
