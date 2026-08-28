<?php

declare(strict_types=1);

namespace Freelo\Sdk\Http;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Represents a paginated API response
 *
 * The Freelo API uses 0-indexed page numbers. Response format:
 * {
 *     "total": 150,
 *     "count": 20,
 *     "page": 0,
 *     "per_page": 20,
 *     "data": { "projects": [...] }
 * }
 *
 * @template T of object
 * @implements IteratorAggregate<int, T>
 */
class PaginatedResult implements IteratorAggregate, Countable
{
    /**
     * @param T[] $items Items on current page
     * @param int $total Total number of items across all pages
     * @param int<0, max> $count Number of items on current page
     * @param int $page Current page number (0-indexed)
     * @param int $perPage Items per page
     */
    public function __construct(
        private readonly array $items,
        private readonly int $total,
        private readonly int $count,
        private readonly int $page,
        private readonly int $perPage,
    ) {
    }

    /**
     * Get all items on current page
     *
     * @return T[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Get total number of items across all pages
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * Get number of items on current page
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Get current page number (0-indexed)
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * Get current page number (0-indexed)
     *
     * @deprecated Use getPage() instead
     */
    public function getCurrentPage(): int
    {
        return $this->page;
    }

    /**
     * Get items per page
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * Get total number of pages
     */
    public function getTotalPages(): int
    {
        if ($this->perPage === 0) {
            return 0;
        }

        return (int) ceil($this->total / $this->perPage);
    }

    /**
     * Check if there is a next page
     */
    public function hasNextPage(): bool
    {
        return $this->page < $this->getTotalPages() - 1;
    }

    /**
     * Check if there is a previous page
     */
    public function hasPreviousPage(): bool
    {
        return $this->page > 0;
    }

    /**
     * Get next page number (or null if last page)
     */
    public function getNextPage(): ?int
    {
        return $this->hasNextPage() ? $this->page + 1 : null;
    }

    /**
     * Get previous page number (or null if first page)
     */
    public function getPreviousPage(): ?int
    {
        return $this->hasPreviousPage() ? $this->page - 1 : null;
    }

    /**
     * Check if this is the first page
     */
    public function isFirstPage(): bool
    {
        return $this->page === 0;
    }

    /**
     * Check if this is the last page
     */
    public function isLastPage(): bool
    {
        return !$this->hasNextPage();
    }

    /**
     * Check if result is empty
     */
    public function isEmpty(): bool
    {
        return $this->count === 0;
    }

    /**
     * Get the count of items on this page (Countable interface)
     */
    public function count(): int
    {
        return $this->count;
    }

    /**
     * Get iterator for items
     *
     * @return Traversable<int, T>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Get first item or null if empty
     *
     * @return T|null
     */
    public function first(): ?object
    {
        return $this->items[0] ?? null;
    }

    /**
     * Get last item or null if empty
     *
     * @return T|null
     */
    public function last(): ?object
    {
        if (empty($this->items)) {
            return null;
        }

        return $this->items[array_key_last($this->items)] ?? null;
    }

    /**
     * Map items through a callback
     *
     * @template U
     * @param callable(T): U $callback
     * @return U[]
     */
    public function map(callable $callback): array
    {
        return array_map($callback, $this->items);
    }

    /**
     * Filter items through a callback
     *
     * @param callable(T): bool $callback
     * @return T[]
     */
    public function filter(callable $callback): array
    {
        return array_values(array_filter($this->items, $callback));
    }

    /**
     * Convert to array representation
     *
     * @return array{items: T[], total: int, count: int, page: int, per_page: int, total_pages: int}
     */
    public function toArray(): array
    {
        return [
            'items' => $this->items,
            'total' => $this->total,
            'count' => $this->count,
            'page' => $this->page,
            'per_page' => $this->perPage,
            'total_pages' => $this->getTotalPages(),
        ];
    }
}
