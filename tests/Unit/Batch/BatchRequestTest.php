<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Batch;

use Freelo\Sdk\Auth\ApiKeyCredentials;
use Freelo\Sdk\Batch\BatchRequest;
use Freelo\Sdk\Http\FreeloClient;
use Freelo\Sdk\Http\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

class BatchRequestTest extends TestCase
{
    private FreeloClient $client;
    private ClientInterface $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(ClientInterface::class);
        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $streamFactory = $this->createMock(StreamFactoryInterface::class);
        $credentials = new ApiKeyCredentials('test-key', 'test@example.com');

        // Setup mocks
        $request = $this->createMock(RequestInterface::class);
        $request->method('withHeader')->willReturnSelf();
        $request->method('withBody')->willReturnSelf();
        $requestFactory->method('createRequest')->willReturn($request);

        $stream = $this->createMock(StreamInterface::class);
        $streamFactory->method('createStream')->willReturn($stream);

        $this->client = new FreeloClient(
            $this->httpClient,
            $requestFactory,
            $streamFactory,
            $credentials,
        );
    }

    public function testAddOperations(): void
    {
        $batch = new BatchRequest($this->client);

        $batch->add('GET', '/projects')
            ->add('POST', '/tasks', ['json' => ['name' => 'Task 1']]);

        $this->assertCount(2, $batch->getOperations());
        $this->assertFalse($batch->isEmpty());
    }

    public function testGetMethod(): void
    {
        $batch = new BatchRequest($this->client);
        $batch->get('/projects', ['page' => 1], 'projects-key');

        $operations = $batch->getOperations();
        $this->assertCount(1, $operations);
        $this->assertEquals('GET', $operations[0]->getMethod());
        $this->assertEquals('/projects', $operations[0]->getUri());
        $this->assertEquals('projects-key', $operations[0]->getKey());
    }

    public function testPostMethod(): void
    {
        $batch = new BatchRequest($this->client);
        $batch->post('/tasks', ['name' => 'Task 1'], 'task-key');

        $operations = $batch->getOperations();
        $this->assertCount(1, $operations);
        $this->assertEquals('POST', $operations[0]->getMethod());
    }

    public function testPutMethod(): void
    {
        $batch = new BatchRequest($this->client);
        $batch->put('/tasks/123', ['name' => 'Updated']);

        $operations = $batch->getOperations();
        $this->assertEquals('PUT', $operations[0]->getMethod());
    }

    public function testPatchMethod(): void
    {
        $batch = new BatchRequest($this->client);
        $batch->patch('/tasks/123', ['status' => 'completed']);

        $operations = $batch->getOperations();
        $this->assertEquals('PATCH', $operations[0]->getMethod());
    }

    public function testDeleteMethod(): void
    {
        $batch = new BatchRequest($this->client);
        $batch->delete('/tasks/123');

        $operations = $batch->getOperations();
        $this->assertEquals('DELETE', $operations[0]->getMethod());
    }

    public function testClear(): void
    {
        $batch = new BatchRequest($this->client);
        $batch->add('GET', '/projects')
            ->add('POST', '/tasks');

        $this->assertCount(2, $batch->getOperations());

        $batch->clear();

        $this->assertCount(0, $batch->getOperations());
        $this->assertTrue($batch->isEmpty());
    }

    public function testCount(): void
    {
        $batch = new BatchRequest($this->client);
        $this->assertEquals(0, $batch->count());

        $batch->add('GET', '/projects');
        $this->assertEquals(1, $batch->count());

        $batch->add('POST', '/tasks');
        $this->assertEquals(2, $batch->count());
    }

    public function testExecute(): void
    {
        // Create a mock response
        $psrResponse = $this->createMock(ResponseInterface::class);
        $psrResponse->method('getStatusCode')->willReturn(200);
        $psrResponse->method('getBody')->willReturn($this->createMock(StreamInterface::class));
        $psrResponse->method('getHeaders')->willReturn([]);

        $this->httpClient->method('sendRequest')->willReturn($psrResponse);

        $batch = new BatchRequest($this->client);
        $batch->get('/projects', [], 'projects')
            ->get('/tasks', [], 'tasks');

        $results = $batch->execute();

        $this->assertEquals(2, $results->count());
        $this->assertEquals(2, $results->successCount());
        $this->assertEquals(0, $results->failureCount());
        $this->assertTrue($results->allSucceeded());
    }
}
