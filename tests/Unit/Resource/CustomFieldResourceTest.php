<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Resource;

use Freelo\Sdk\Http\FreeloClient;
use Freelo\Sdk\Http\Response;
use Freelo\Sdk\Http\ResponseParser;
use Freelo\Sdk\Resource\CustomFieldResource;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class CustomFieldResourceTest extends TestCase
{
    private FreeloClient $client;
    private ResponseParser $parser;
    private CustomFieldResource $resource;

    protected function setUp(): void
    {
        $this->client = $this->createMock(FreeloClient::class);
        $this->parser = new ResponseParser();

        $this->client->method('getResponseParser')
            ->willReturn($this->parser);

        $this->resource = new CustomFieldResource($this->client);
    }

    public function testAddOrEditValue(): void
    {
        $data = [
            'custom_field_uuid' => 'cf-uuid-1',
            'task_id' => 123,
            'value' => 'Invoice #42',
        ];
        $responseData = [
            'custom_field_value' => ['uuid' => 'value-uuid-1', 'value' => 'Invoice #42'],
        ];

        $this->client->expects($this->once())
            ->method('post')
            ->with('custom-field/add-or-edit-value', $data)
            ->willReturn($this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR)));

        $result = $this->resource->addOrEditValue($data);

        $this->assertSame('value-uuid-1', $result['custom_field_value']['uuid']);
    }

    public function testAddValue(): void
    {
        $data = [
            'custom_field_uuid' => 'cf-uuid-1',
            'value' => 'Invoice #42',
        ];
        $responseData = [
            'custom_field_value' => ['uuid' => 'value-uuid-1', 'value' => 'Invoice #42'],
        ];

        $this->client->expects($this->once())
            ->method('post')
            ->with('custom-field/add-value/123', $data)
            ->willReturn($this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR)));

        $result = $this->resource->addValue(123, $data);

        $this->assertSame('value-uuid-1', $result['custom_field_value']['uuid']);
    }

    public function testChangeValue(): void
    {
        $responseData = [
            'custom_field_value' => ['uuid' => 'value-uuid-1', 'value' => 'Invoice #43'],
        ];

        $this->client->expects($this->once())
            ->method('post')
            ->with('custom-field/change-value/value-uuid-1', ['value' => 'Invoice #43'])
            ->willReturn($this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR)));

        $result = $this->resource->changeValue('value-uuid-1', 'Invoice #43');

        $this->assertSame('Invoice #43', $result['custom_field_value']['value']);
    }

    public function testAddEnumValue(): void
    {
        // The body key is camelCase here — the endpoint's own quirk, not a typo.
        $data = [
            'customFieldUuid' => 'cf-uuid-1',
            'value' => 'enum-option-uuid-1',
        ];
        $responseData = [
            'custom_field_value' => ['uuid' => 'value-uuid-1', 'value' => 'enum-option-uuid-1'],
        ];

        $this->client->expects($this->once())
            ->method('post')
            ->with('custom-field/add-enum-value/123', $data)
            ->willReturn($this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR)));

        $result = $this->resource->addEnumValue(123, $data);

        $this->assertSame('enum-option-uuid-1', $result['custom_field_value']['value']);
    }

    public function testChangeEnumValue(): void
    {
        $responseData = [
            'custom_field_value' => ['uuid' => 'value-uuid-1', 'value' => 'enum-option-uuid-2'],
        ];

        $this->client->expects($this->once())
            ->method('post')
            ->with('custom-field/change-enum-value/value-uuid-1', ['value' => 'enum-option-uuid-2'])
            ->willReturn($this->createSuccessResponse(json_encode($responseData, JSON_THROW_ON_ERROR)));

        $result = $this->resource->changeEnumValue('value-uuid-1', 'enum-option-uuid-2');

        $this->assertSame('enum-option-uuid-2', $result['custom_field_value']['value']);
    }

    public function testDeleteValue(): void
    {
        $this->client->expects($this->once())
            ->method('delete')
            ->with('custom-field/delete-value/value-uuid-1')
            ->willReturn($this->createSuccessResponse('', 200));

        $this->assertTrue($this->resource->deleteValue('value-uuid-1'));
    }

    private function createSuccessResponse(string $body, int $statusCode = 200): Response
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn($body);

        $psrResponse = $this->createMock(ResponseInterface::class);
        $psrResponse->method('getStatusCode')->willReturn($statusCode);
        $psrResponse->method('getBody')->willReturn($stream);

        return new Response($psrResponse);
    }
}
