<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Http;

use Freelo\Sdk\Http\PaginatedResult;
use PHPUnit\Framework\TestCase;
use stdClass;

class PaginatedResultTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $items = [$this->createItem(1), $this->createItem(2)];
        $result = new PaginatedResult(
            items: $items,
            total: 100,
            count: 2,
            page: 0,
            perPage: 20,
        );

        $this->assertSame($items, $result->getItems());
        $this->assertSame(100, $result->getTotal());
        $this->assertSame(2, $result->getCount());
        $this->assertSame(0, $result->getPage());
        $this->assertSame(0, $result->getCurrentPage()); // Deprecated alias
        $this->assertSame(20, $result->getPerPage());
    }

    public function testGetTotalPages(): void
    {
        $result = new PaginatedResult(
            items: [],
            total: 100,
            count: 0,
            page: 0,
            perPage: 20,
        );

        $this->assertSame(5, $result->getTotalPages());
    }

    public function testGetTotalPagesWithRemainder(): void
    {
        $result = new PaginatedResult(
            items: [],
            total: 45,
            count: 0,
            page: 0,
            perPage: 20,
        );

        $this->assertSame(3, $result->getTotalPages());
    }

    public function testGetTotalPagesWithZeroPerPage(): void
    {
        $result = new PaginatedResult(
            items: [],
            total: 100,
            count: 0,
            page: 0,
            perPage: 0,
        );

        $this->assertSame(0, $result->getTotalPages());
    }

    public function testHasNextPageOnFirstPage(): void
    {
        $result = new PaginatedResult(
            items: [$this->createItem(1)],
            total: 100,
            count: 1,
            page: 0,
            perPage: 20,
        );

        $this->assertTrue($result->hasNextPage());
    }

    public function testHasNextPageOnLastPage(): void
    {
        $result = new PaginatedResult(
            items: [$this->createItem(1)],
            total: 100,
            count: 1,
            page: 4, // Last page (0-indexed, 5 pages total)
            perPage: 20,
        );

        $this->assertFalse($result->hasNextPage());
    }

    public function testHasPreviousPageOnFirstPage(): void
    {
        $result = new PaginatedResult(
            items: [$this->createItem(1)],
            total: 100,
            count: 1,
            page: 0,
            perPage: 20,
        );

        $this->assertFalse($result->hasPreviousPage());
    }

    public function testHasPreviousPageOnSecondPage(): void
    {
        $result = new PaginatedResult(
            items: [$this->createItem(1)],
            total: 100,
            count: 1,
            page: 1,
            perPage: 20,
        );

        $this->assertTrue($result->hasPreviousPage());
    }

    public function testGetNextPage(): void
    {
        $result = new PaginatedResult(
            items: [$this->createItem(1)],
            total: 100,
            count: 1,
            page: 2,
            perPage: 20,
        );

        $this->assertSame(3, $result->getNextPage());
    }

    public function testGetNextPageOnLastPage(): void
    {
        $result = new PaginatedResult(
            items: [$this->createItem(1)],
            total: 100,
            count: 1,
            page: 4,
            perPage: 20,
        );

        $this->assertNull($result->getNextPage());
    }

    public function testGetPreviousPage(): void
    {
        $result = new PaginatedResult(
            items: [$this->createItem(1)],
            total: 100,
            count: 1,
            page: 2,
            perPage: 20,
        );

        $this->assertSame(1, $result->getPreviousPage());
    }

    public function testGetPreviousPageOnFirstPage(): void
    {
        $result = new PaginatedResult(
            items: [$this->createItem(1)],
            total: 100,
            count: 1,
            page: 0,
            perPage: 20,
        );

        $this->assertNull($result->getPreviousPage());
    }

    public function testIsFirstPage(): void
    {
        $resultFirst = new PaginatedResult(
            items: [$this->createItem(1)],
            total: 100,
            count: 1,
            page: 0,
            perPage: 20,
        );

        $resultNotFirst = new PaginatedResult(
            items: [$this->createItem(1)],
            total: 100,
            count: 1,
            page: 1,
            perPage: 20,
        );

        $this->assertTrue($resultFirst->isFirstPage());
        $this->assertFalse($resultNotFirst->isFirstPage());
    }

    public function testIsLastPage(): void
    {
        $resultLast = new PaginatedResult(
            items: [$this->createItem(1)],
            total: 100,
            count: 1,
            page: 4,
            perPage: 20,
        );

        $resultNotLast = new PaginatedResult(
            items: [$this->createItem(1)],
            total: 100,
            count: 1,
            page: 0,
            perPage: 20,
        );

        $this->assertTrue($resultLast->isLastPage());
        $this->assertFalse($resultNotLast->isLastPage());
    }

    public function testIsEmpty(): void
    {
        $resultEmpty = new PaginatedResult(
            items: [],
            total: 0,
            count: 0,
            page: 0,
            perPage: 20,
        );

        $resultNotEmpty = new PaginatedResult(
            items: [$this->createItem(1)],
            total: 100,
            count: 1,
            page: 0,
            perPage: 20,
        );

        $this->assertTrue($resultEmpty->isEmpty());
        $this->assertFalse($resultNotEmpty->isEmpty());
    }

    public function testCount(): void
    {
        $result = new PaginatedResult(
            items: [$this->createItem(1), $this->createItem(2)],
            total: 100,
            count: 2,
            page: 0,
            perPage: 20,
        );

        $this->assertCount(2, $result);
        $this->assertSame(2, $result->count());
    }

    public function testGetIterator(): void
    {
        $items = [$this->createItem(1), $this->createItem(2)];
        $result = new PaginatedResult(
            items: $items,
            total: 100,
            count: 2,
            page: 0,
            perPage: 20,
        );

        $iterated = [];
        foreach ($result as $item) {
            $iterated[] = $item;
        }

        $this->assertSame($items, $iterated);
    }

    public function testFirst(): void
    {
        $items = [$this->createItem(1), $this->createItem(2)];
        $result = new PaginatedResult(
            items: $items,
            total: 100,
            count: 2,
            page: 0,
            perPage: 20,
        );

        $this->assertSame($items[0], $result->first());
    }

    public function testFirstOnEmpty(): void
    {
        $result = new PaginatedResult(
            items: [],
            total: 0,
            count: 0,
            page: 0,
            perPage: 20,
        );

        $this->assertNull($result->first());
    }

    public function testLast(): void
    {
        $items = [$this->createItem(1), $this->createItem(2)];
        $result = new PaginatedResult(
            items: $items,
            total: 100,
            count: 2,
            page: 0,
            perPage: 20,
        );

        $this->assertSame($items[1], $result->last());
    }

    public function testLastOnEmpty(): void
    {
        $result = new PaginatedResult(
            items: [],
            total: 0,
            count: 0,
            page: 0,
            perPage: 20,
        );

        $this->assertNull($result->last());
    }

    public function testMap(): void
    {
        $items = [$this->createItem(1), $this->createItem(2)];
        $result = new PaginatedResult(
            items: $items,
            total: 100,
            count: 2,
            page: 0,
            perPage: 20,
        );

        $mapped = $result->map(fn($item) => $item->id);

        $this->assertSame([1, 2], $mapped);
    }

    public function testFilter(): void
    {
        $items = [$this->createItem(1), $this->createItem(2), $this->createItem(3)];
        $result = new PaginatedResult(
            items: $items,
            total: 100,
            count: 3,
            page: 0,
            perPage: 20,
        );

        $filtered = $result->filter(fn($item) => $item->id > 1);

        $this->assertCount(2, $filtered);
        $this->assertSame(2, $filtered[0]->id);
        $this->assertSame(3, $filtered[1]->id);
    }

    public function testToArray(): void
    {
        $items = [$this->createItem(1), $this->createItem(2)];
        $result = new PaginatedResult(
            items: $items,
            total: 100,
            count: 2,
            page: 0,
            perPage: 20,
        );

        $array = $result->toArray();

        $this->assertSame($items, $array['items']);
        $this->assertSame(100, $array['total']);
        $this->assertSame(2, $array['count']);
        $this->assertSame(0, $array['page']);
        $this->assertSame(20, $array['per_page']);
        $this->assertSame(5, $array['total_pages']);
    }

    public function testSinglePageResult(): void
    {
        $items = [$this->createItem(1)];
        $result = new PaginatedResult(
            items: $items,
            total: 1,
            count: 1,
            page: 0,
            perPage: 20,
        );

        $this->assertSame(1, $result->getTotalPages());
        $this->assertTrue($result->isFirstPage());
        $this->assertTrue($result->isLastPage());
        $this->assertFalse($result->hasNextPage());
        $this->assertFalse($result->hasPreviousPage());
        $this->assertNull($result->getNextPage());
        $this->assertNull($result->getPreviousPage());
    }

    private function createItem(int $id): object
    {
        $item = new stdClass();
        $item->id = $id;
        return $item;
    }
}
