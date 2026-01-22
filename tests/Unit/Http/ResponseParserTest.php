<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Http;

use Freelo\Sdk\Exception\ApiException;
use Freelo\Sdk\Http\PaginatedResult;
use Freelo\Sdk\Http\Response;
use Freelo\Sdk\Http\ResponseParser;
use Freelo\Sdk\Model\Project;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ResponseParserTest extends TestCase
{
    private ResponseParser $parser;

    protected function setUp(): void
    {
        $this->parser = new ResponseParser();
    }

    public function testParseSingleWithDirectData(): void
    {
        $response = $this->createResponse(200, '{"id":1,"name":"Test"}');
        $data = $this->parser->parseSingle($response);

        $this->assertIsArray($data);
        $this->assertSame(1, $data['id']);
        $this->assertSame('Test', $data['name']);
    }

    public function testParseSingleWithWrappedData(): void
    {
        $response = $this->createResponse(200, '{"data":{"id":1,"name":"Test"}}');
        $data = $this->parser->parseSingle($response);

        $this->assertIsArray($data);
        $this->assertSame(1, $data['id']);
        $this->assertSame('Test', $data['name']);
    }

    public function testParseSingleWithNestedData(): void
    {
        // Handles format like {"data": {"project": {...}}}
        $response = $this->createResponse(200, '{"data":{"project":{"id":1,"name":"Test"}}}');
        $data = $this->parser->parseSingle($response);

        $this->assertIsArray($data);
        $this->assertSame(1, $data['id']);
        $this->assertSame('Test', $data['name']);
    }

    public function testParseSingleThrowsOnInvalidJson(): void
    {
        $response = $this->createResponse(200, 'invalid json');

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Failed to parse JSON response');
        $this->parser->parseSingle($response);
    }

    public function testParseCollectionWithDirectArray(): void
    {
        $response = $this->createResponse(200, '[{"id":1},{"id":2}]');
        $data = $this->parser->parseCollection($response);

        $this->assertIsArray($data);
        $this->assertCount(2, $data);
        $this->assertSame(1, $data[0]['id']);
        $this->assertSame(2, $data[1]['id']);
    }

    public function testParseCollectionWithWrappedData(): void
    {
        $response = $this->createResponse(200, '{"data":[{"id":1},{"id":2}]}');
        $data = $this->parser->parseCollection($response);

        $this->assertIsArray($data);
        $this->assertCount(2, $data);
        $this->assertSame(1, $data[0]['id']);
        $this->assertSame(2, $data[1]['id']);
    }

    public function testParseCollectionWithNestedData(): void
    {
        // Handles format like {"data": {"projects": [...]}}
        $response = $this->createResponse(200, '{"data":{"projects":[{"id":1},{"id":2}]}}');
        $data = $this->parser->parseCollection($response);

        $this->assertIsArray($data);
        $this->assertCount(2, $data);
        $this->assertSame(1, $data[0]['id']);
        $this->assertSame(2, $data[1]['id']);
    }

    public function testParseCollectionWithDataKey(): void
    {
        $response = $this->createResponse(200, '{"data":{"tasks":[{"id":1}],"projects":[{"id":2}]}}');
        $data = $this->parser->parseCollection($response, 'projects');

        $this->assertIsArray($data);
        $this->assertCount(1, $data);
        $this->assertSame(2, $data[0]['id']);
    }

    public function testParseCollectionWithSingleItem(): void
    {
        $response = $this->createResponse(200, '{"id":1,"name":"Test"}');
        $data = $this->parser->parseCollection($response);

        $this->assertIsArray($data);
        $this->assertCount(1, $data);
        $this->assertSame(1, $data[0]['id']);
    }

    public function testParseCollectionWithEmptyArray(): void
    {
        $response = $this->createResponse(200, '[]');
        $data = $this->parser->parseCollection($response);

        $this->assertIsArray($data);
        $this->assertEmpty($data);
    }

    public function testParseCollectionThrowsOnInvalidJson(): void
    {
        $response = $this->createResponse(200, 'invalid json');

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Failed to parse JSON response');
        $this->parser->parseCollection($response);
    }

    public function testParsePaginatedRawWithFreeloApiFormat(): void
    {
        // Test Freelo API format: page (0-indexed), count, total, per_page, nested data
        $response = $this->createResponse(200, json_encode([
            'total' => 50,
            'count' => 2,
            'page' => 0,
            'per_page' => 20,
            'data' => [
                'projects' => [
                    ['id' => 1],
                    ['id' => 2],
                ],
            ],
        ], JSON_THROW_ON_ERROR));

        $result = $this->parser->parsePaginatedRaw($response);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('pagination', $result);

        $this->assertCount(2, $result['data']);
        $this->assertSame(0, $result['pagination']['page']);
        $this->assertSame(2, $result['pagination']['count']);
        $this->assertSame(20, $result['pagination']['per_page']);
        $this->assertSame(50, $result['pagination']['total']);
    }

    public function testParsePaginatedRawWithDataKey(): void
    {
        $response = $this->createResponse(200, json_encode([
            'total' => 10,
            'count' => 2,
            'page' => 1,
            'per_page' => 2,
            'data' => [
                'tasks' => [['id' => 100]],
                'projects' => [['id' => 1], ['id' => 2]],
            ],
        ], JSON_THROW_ON_ERROR));

        $result = $this->parser->parsePaginatedRaw($response, 'projects');

        $this->assertCount(2, $result['data']);
        $this->assertSame(1, $result['data'][0]['id']);
        $this->assertSame(2, $result['data'][1]['id']);
    }

    public function testParsePaginatedRawWithDirectDataArray(): void
    {
        // Some endpoints might return data directly as an array
        $response = $this->createResponse(200, json_encode([
            'total' => 2,
            'count' => 2,
            'page' => 0,
            'per_page' => 20,
            'data' => [
                ['id' => 1],
                ['id' => 2],
            ],
        ], JSON_THROW_ON_ERROR));

        $result = $this->parser->parsePaginatedRaw($response);

        $this->assertCount(2, $result['data']);
        $this->assertSame(1, $result['data'][0]['id']);
    }

    public function testParsePaginatedRawWithMissingPaginationData(): void
    {
        $response = $this->createResponse(200, '{"data":{"items":[{"id":1}]}}');
        $result = $this->parser->parsePaginatedRaw($response);

        $this->assertIsArray($result);
        $this->assertSame(0, $result['pagination']['page']);
        $this->assertSame(20, $result['pagination']['per_page']);
        $this->assertSame(0, $result['pagination']['total']);
        // Count should be calculated from items
        $this->assertSame(1, $result['pagination']['count']);
    }

    public function testParsePaginatedRawWithEmptyData(): void
    {
        $response = $this->createResponse(200, '{}');
        $result = $this->parser->parsePaginatedRaw($response);

        $this->assertIsArray($result);
        $this->assertEmpty($result['data']);
        $this->assertSame(20, $result['pagination']['per_page']);
        $this->assertSame(0, $result['pagination']['total']);
    }

    public function testParsePaginatedRawThrowsOnInvalidJson(): void
    {
        $response = $this->createResponse(200, 'invalid json');

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Failed to parse JSON response');
        $this->parser->parsePaginatedRaw($response);
    }

    public function testParsePaginatedWithModelClass(): void
    {
        $response = $this->createResponse(200, json_encode([
            'total' => 40,
            'count' => 2,
            'page' => 0,
            'per_page' => 20,
            'data' => [
                'projects' => [
                    ['id' => 1, 'name' => 'Project 1'],
                    ['id' => 2, 'name' => 'Project 2'],
                ],
            ],
        ], JSON_THROW_ON_ERROR));

        $result = $this->parser->parsePaginated($response, Project::class, 'projects');

        $this->assertInstanceOf(PaginatedResult::class, $result);
        $this->assertCount(2, $result);
        $this->assertSame(0, $result->getPage());
        $this->assertSame(0, $result->getCurrentPage()); // Deprecated alias
        $this->assertSame(2, $result->getTotalPages()); // 40 total / 20 per page = 2 pages
        $this->assertSame(20, $result->getPerPage());
        $this->assertSame(40, $result->getTotal());
        $this->assertSame(2, $result->getCount());

        $items = $result->getItems();
        $this->assertContainsOnlyInstancesOf(Project::class, $items);
        $this->assertSame(1, $items[0]->id);
        $this->assertSame('Project 1', $items[0]->name);
    }

    public function testParsePaginatedWithAutoDetection(): void
    {
        // When no dataKey is provided, should auto-detect from nested data
        $response = $this->createResponse(200, json_encode([
            'total' => 20,
            'count' => 2,
            'page' => 0,
            'per_page' => 20,
            'data' => [
                'projects' => [
                    ['id' => 1, 'name' => 'Project 1'],
                    ['id' => 2, 'name' => 'Project 2'],
                ],
            ],
        ], JSON_THROW_ON_ERROR));

        $result = $this->parser->parsePaginated($response, Project::class);

        $this->assertInstanceOf(PaginatedResult::class, $result);
        $this->assertCount(2, $result);

        $items = $result->getItems();
        $this->assertSame(1, $items[0]->id);
    }

    public function testParseBooleanReturnsTrue(): void
    {
        $response = $this->createResponse(200, '');
        $result = $this->parser->parseBoolean($response);

        $this->assertTrue($result);
    }

    public function testParseBooleanReturnsTrueFor204(): void
    {
        $response = $this->createResponse(204, '');
        $result = $this->parser->parseBoolean($response);

        $this->assertTrue($result);
    }

    public function testParseBooleanReturnsTrueForSuccessResult(): void
    {
        $response = $this->createResponse(200, '{"result":"success"}');
        $result = $this->parser->parseBoolean($response);

        $this->assertTrue($result);
    }

    public function testParseBooleanThrowsOnError(): void
    {
        $response = $this->createResponse(404, '');

        $this->expectException(ApiException::class);
        $this->parser->parseBoolean($response);
    }

    private function createResponse(int $statusCode, string $body): Response
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn($body);

        $psrResponse = $this->createMock(ResponseInterface::class);
        $psrResponse->method('getStatusCode')->willReturn($statusCode);
        $psrResponse->method('getBody')->willReturn($stream);

        return new Response($psrResponse);
    }
}
