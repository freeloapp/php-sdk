<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Http;

use Freelo\Sdk\Http\PaginatedResult;
use Freelo\Sdk\Http\Paginator;
use PHPUnit\Framework\TestCase;
use stdClass;

class PaginatorTest extends TestCase
{
    public function testFetchAllIteratesAllPages(): void
    {
        $fetcher = $this->createFetcher([
            0 => $this->createPaginatedResult([1, 2], total: 5, count: 2, page: 0, perPage: 2),
            1 => $this->createPaginatedResult([3, 4], total: 5, count: 2, page: 1, perPage: 2),
            2 => $this->createPaginatedResult([5], total: 5, count: 1, page: 2, perPage: 2),
        ]);

        $items = [];
        foreach (Paginator::fetchAll($fetcher) as $item) {
            $items[] = $item->id;
        }

        $this->assertSame([1, 2, 3, 4, 5], $items);
    }

    public function testFetchAllWithSinglePage(): void
    {
        $fetcher = $this->createFetcher([
            0 => $this->createPaginatedResult([1, 2, 3], total: 3, count: 3, page: 0, perPage: 20),
        ]);

        $items = [];
        foreach (Paginator::fetchAll($fetcher) as $item) {
            $items[] = $item->id;
        }

        $this->assertSame([1, 2, 3], $items);
    }

    public function testFetchAllWithEmptyResult(): void
    {
        $fetcher = $this->createFetcher([
            0 => $this->createPaginatedResult([], total: 0, count: 0, page: 0, perPage: 20),
        ]);

        $items = [];
        foreach (Paginator::fetchAll($fetcher) as $item) {
            $items[] = $item->id;
        }

        $this->assertEmpty($items);
    }

    public function testCollectAllReturnsArray(): void
    {
        $fetcher = $this->createFetcher([
            0 => $this->createPaginatedResult([1, 2], total: 4, count: 2, page: 0, perPage: 2),
            1 => $this->createPaginatedResult([3, 4], total: 4, count: 2, page: 1, perPage: 2),
        ]);

        $items = Paginator::collectAll($fetcher);

        $this->assertCount(4, $items);
        $this->assertSame(1, $items[0]->id);
        $this->assertSame(4, $items[3]->id);
    }

    public function testFetchRangeWithinBounds(): void
    {
        $fetcher = $this->createFetcher([
            1 => $this->createPaginatedResult([3, 4], total: 10, count: 2, page: 1, perPage: 2),
            2 => $this->createPaginatedResult([5, 6], total: 10, count: 2, page: 2, perPage: 2),
        ]);

        $items = [];
        foreach (Paginator::fetchRange($fetcher, 1, 2) as $item) {
            $items[] = $item->id;
        }

        $this->assertSame([3, 4, 5, 6], $items);
    }

    public function testFetchRangeStopsAtLastPage(): void
    {
        $fetcher = $this->createFetcher([
            0 => $this->createPaginatedResult([1, 2], total: 3, count: 2, page: 0, perPage: 2),
            1 => $this->createPaginatedResult([3], total: 3, count: 1, page: 1, perPage: 2),
        ]);

        $items = [];
        foreach (Paginator::fetchRange($fetcher, 0, 10) as $item) {
            $items[] = $item->id;
        }

        // Should stop at page 1 even though range goes to 10
        $this->assertSame([1, 2, 3], $items);
    }

    public function testFetchUpToLimitsItems(): void
    {
        $fetcher = $this->createFetcher([
            0 => $this->createPaginatedResult([1, 2], total: 10, count: 2, page: 0, perPage: 2),
            1 => $this->createPaginatedResult([3, 4], total: 10, count: 2, page: 1, perPage: 2),
            2 => $this->createPaginatedResult([5, 6], total: 10, count: 2, page: 2, perPage: 2),
        ]);

        $items = [];
        foreach (Paginator::fetchUpTo($fetcher, 5) as $item) {
            $items[] = $item->id;
        }

        $this->assertSame([1, 2, 3, 4, 5], $items);
    }

    public function testFetchUpToWithLimitExceedingTotal(): void
    {
        $fetcher = $this->createFetcher([
            0 => $this->createPaginatedResult([1, 2, 3], total: 3, count: 3, page: 0, perPage: 20),
        ]);

        $items = [];
        foreach (Paginator::fetchUpTo($fetcher, 100) as $item) {
            $items[] = $item->id;
        }

        $this->assertSame([1, 2, 3], $items);
    }

    public function testGetTotalCount(): void
    {
        $fetcher = $this->createFetcher([
            0 => $this->createPaginatedResult([1, 2], total: 150, count: 2, page: 0, perPage: 20),
        ]);

        $total = Paginator::getTotalCount($fetcher);

        $this->assertSame(150, $total);
    }

    /**
     * @param array<int, PaginatedResult<object>> $pages
     * @return callable(int): PaginatedResult<object>
     */
    private function createFetcher(array $pages): callable
    {
        return function (int $page) use ($pages): PaginatedResult {
            if (!isset($pages[$page])) {
                throw new \RuntimeException("Unexpected page: $page");
            }
            return $pages[$page];
        };
    }

    /**
     * @param int[] $ids
     * @return PaginatedResult<object>
     */
    private function createPaginatedResult(array $ids, int $total, int $count, int $page, int $perPage): PaginatedResult
    {
        $items = array_map(function (int $id): object {
            $item = new stdClass();
            $item->id = $id;
            return $item;
        }, $ids);

        return new PaginatedResult(
            items: $items,
            total: $total,
            count: $count,
            page: $page,
            perPage: $perPage,
        );
    }
}
