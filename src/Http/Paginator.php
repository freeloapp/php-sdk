<?php

declare(strict_types=1);

namespace Freelo\Sdk\Http;

use Generator;

/**
 * Helper for iterating through all pages of a paginated endpoint
 *
 * Example usage:
 * ```php
 * use Freelo\Sdk\Http\Paginator;
 *
 * // Iterate through all projects lazily (memory efficient)
 * foreach (Paginator::fetchAll(fn($page) => $freelo->projects()->getAll(['p' => $page])) as $project) {
 *     echo $project->name . "\n";
 * }
 *
 * // Collect all projects into array
 * $allProjects = Paginator::collectAll(fn($page) => $freelo->projects()->getAll(['p' => $page]));
 * ```
 */
class Paginator
{
    /**
     * Iterate through all pages of a paginated endpoint
     *
     * This method yields items lazily, making it memory-efficient for large datasets.
     * Items are fetched page by page as they are consumed.
     *
     * @template T of object
     * @param callable(int): PaginatedResult<T> $fetcher Function that takes page number and returns PaginatedResult
     * @return Generator<int, T, mixed, void>
     */
    public static function fetchAll(callable $fetcher): Generator
    {
        $page = 0;

        do {
            $result = $fetcher($page);

            foreach ($result->getItems() as $item) {
                yield $item;
            }

            $page++;
        } while ($result->hasNextPage());
    }

    /**
     * Iterate through all pages and collect into array
     *
     * Note: This loads all items into memory. For large datasets,
     * consider using fetchAll() with a generator instead.
     *
     * @template T of object
     * @param callable(int): PaginatedResult<T> $fetcher Function that takes page number and returns PaginatedResult
     * @return T[]
     */
    public static function collectAll(callable $fetcher): array
    {
        return iterator_to_array(self::fetchAll($fetcher), false);
    }

    /**
     * Fetch a specific page range
     *
     * Useful when you only need a subset of pages.
     *
     * @template T of object
     * @param callable(int): PaginatedResult<T> $fetcher Function that takes page number and returns PaginatedResult
     * @param int $startPage Starting page number (0-indexed)
     * @param int $endPage Ending page number (inclusive, 0-indexed)
     * @return Generator<int, T, mixed, void>
     */
    public static function fetchRange(callable $fetcher, int $startPage, int $endPage): Generator
    {
        for ($page = $startPage; $page <= $endPage; $page++) {
            $result = $fetcher($page);

            foreach ($result->getItems() as $item) {
                yield $item;
            }

            // Stop early if we've reached the last page
            if ($result->isLastPage()) {
                break;
            }
        }
    }

    /**
     * Fetch items up to a maximum count
     *
     * Useful when you need at most N items without knowing the page structure.
     *
     * @template T of object
     * @param callable(int): PaginatedResult<T> $fetcher Function that takes page number and returns PaginatedResult
     * @param int $maxItems Maximum number of items to fetch
     * @return Generator<int, T, mixed, void>
     */
    public static function fetchUpTo(callable $fetcher, int $maxItems): Generator
    {
        $page = 0;
        $count = 0;

        do {
            $result = $fetcher($page);

            foreach ($result->getItems() as $item) {
                yield $item;
                $count++;

                if ($count >= $maxItems) {
                    return;
                }
            }

            $page++;
        } while ($result->hasNextPage());
    }

    /**
     * Get total count without loading all items
     *
     * Makes a single request to get the total count from pagination metadata.
     *
     * @template T of object
     * @param callable(int): PaginatedResult<T> $fetcher Function that takes page number and returns PaginatedResult
     * @return int Total number of items across all pages
     */
    public static function getTotalCount(callable $fetcher): int
    {
        $result = $fetcher(0);
        return $result->getTotal();
    }
}
