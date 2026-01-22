<?php

declare(strict_types=1);

namespace Freelo\Sdk\Batch;

use Freelo\Sdk\Http\FreeloClient;
use Freelo\Sdk\Exception\ApiException;

/**
 * Batch request handler for executing multiple operations
 *
 * Allows queuing multiple API requests and executing them efficiently.
 */
class BatchRequest
{
    /**
     * @var array<int, BatchOperation>
     */
    private array $operations = [];

    /**
     * @var array<int, BatchResult>
     */
    private array $results = [];

    public function __construct(
        private readonly FreeloClient $client,
    ) {
    }

    /**
     * Add an operation to the batch
     *
     * @param string $method HTTP method (GET, POST, PUT, PATCH, DELETE)
     * @param string $uri Request URI
     * @param array<string, mixed> $options Request options
     * @param string|null $key Optional key to identify this operation
     * @return $this
     */
    public function add(string $method, string $uri, array $options = [], ?string $key = null): self
    {
        $operation = new BatchOperation($method, $uri, $options, $key);
        $this->operations[] = $operation;

        return $this;
    }

    /**
     * Add a GET request to the batch
     *
     * @param string $uri Request URI
     * @param array<string, mixed> $queryParams Query parameters
     * @param string|null $key Optional key to identify this operation
     * @return $this
     */
    public function get(string $uri, array $queryParams = [], ?string $key = null): self
    {
        return $this->add('GET', $uri, ['query' => $queryParams], $key);
    }

    /**
     * Add a POST request to the batch
     *
     * @param string $uri Request URI
     * @param array<string, mixed> $data Request body data
     * @param string|null $key Optional key to identify this operation
     * @return $this
     */
    public function post(string $uri, array $data = [], ?string $key = null): self
    {
        return $this->add('POST', $uri, ['json' => $data], $key);
    }

    /**
     * Add a PUT request to the batch
     *
     * @param string $uri Request URI
     * @param array<string, mixed> $data Request body data
     * @param string|null $key Optional key to identify this operation
     * @return $this
     */
    public function put(string $uri, array $data = [], ?string $key = null): self
    {
        return $this->add('PUT', $uri, ['json' => $data], $key);
    }

    /**
     * Add a PATCH request to the batch
     *
     * @param string $uri Request URI
     * @param array<string, mixed> $data Request body data
     * @param string|null $key Optional key to identify this operation
     * @return $this
     */
    public function patch(string $uri, array $data = [], ?string $key = null): self
    {
        return $this->add('PATCH', $uri, ['json' => $data], $key);
    }

    /**
     * Add a DELETE request to the batch
     *
     * @param string $uri Request URI
     * @param string|null $key Optional key to identify this operation
     * @return $this
     */
    public function delete(string $uri, ?string $key = null): self
    {
        return $this->add('DELETE', $uri, [], $key);
    }

    /**
     * Execute all queued operations
     *
     * @param bool $stopOnError Stop executing remaining operations if one fails
     * @return BatchResults Results of all operations
     */
    public function execute(bool $stopOnError = false): BatchResults
    {
        $this->results = [];

        foreach ($this->operations as $index => $operation) {
            try {
                $response = $this->client->request(
                    $operation->getMethod(),
                    $operation->getUri(),
                    $operation->getOptions(),
                );

                $result = BatchResult::success($operation, $response);
                $this->results[] = $result;
            } catch (ApiException $e) {
                $result = BatchResult::failure($operation, $e);
                $this->results[] = $result;

                if ($stopOnError) {
                    break;
                }
            }
        }

        return new BatchResults($this->results);
    }

    /**
     * Clear all queued operations
     *
     * @return $this
     */
    public function clear(): self
    {
        $this->operations = [];
        $this->results = [];

        return $this;
    }

    /**
     * Get all queued operations
     *
     * @return array<int, BatchOperation>
     */
    public function getOperations(): array
    {
        return $this->operations;
    }

    /**
     * Get the number of queued operations
     */
    public function count(): int
    {
        return count($this->operations);
    }

    /**
     * Check if there are any queued operations
     */
    public function isEmpty(): bool
    {
        return count($this->operations) === 0;
    }
}
