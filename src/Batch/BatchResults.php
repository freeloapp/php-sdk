<?php

declare(strict_types=1);

namespace Freelo\Sdk\Batch;

/**
 * Collection of batch operation results
 *
 * @implements \IteratorAggregate<int, BatchResult>
 */
class BatchResults implements \Countable, \IteratorAggregate
{
    /**
     * @param array<int, BatchResult> $results
     */
    public function __construct(
        private readonly array $results,
    ) {
    }

    /**
     * Get all results
     *
     * @return array<int, BatchResult>
     */
    public function all(): array
    {
        return $this->results;
    }

    /**
     * Get successful results
     *
     * @return array<int, BatchResult>
     */
    public function successful(): array
    {
        return array_filter($this->results, fn(BatchResult $result) => $result->isSuccess());
    }

    /**
     * Get failed results
     *
     * @return array<int, BatchResult>
     */
    public function failed(): array
    {
        return array_filter($this->results, fn(BatchResult $result) => $result->isFailure());
    }

    /**
     * Get a result by its key
     */
    public function get(string $key): ?BatchResult
    {
        foreach ($this->results as $result) {
            if ($result->getKey() === $key) {
                return $result;
            }
        }

        return null;
    }

    /**
     * Check if all operations succeeded
     */
    public function allSucceeded(): bool
    {
        return count($this->failed()) === 0;
    }

    /**
     * Check if any operations failed
     */
    public function hasFailures(): bool
    {
        return count($this->failed()) > 0;
    }

    /**
     * Get the number of successful operations
     */
    public function successCount(): int
    {
        return count($this->successful());
    }

    /**
     * Get the number of failed operations
     */
    public function failureCount(): int
    {
        return count($this->failed());
    }

    /**
     * Get the total number of results
     */
    public function count(): int
    {
        return count($this->results);
    }

    /**
     * Get an iterator for the results
     *
     * @return \ArrayIterator<int, BatchResult>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->results);
    }

    /**
     * Convert results to array
     *
     * @return array<int, array{
     *     operation: array<string, mixed>,
     *     success: bool,
     *     response?: array<string, mixed>,
     *     error?: string
     * }>
     */
    public function toArray(): array
    {
        return array_map(function (BatchResult $result) {
            $data = [
                'operation' => [
                    'method' => $result->getOperation()->getMethod(),
                    'uri' => $result->getOperation()->getUri(),
                    'key' => $result->getOperation()->getKey(),
                ],
                'success' => $result->isSuccess(),
            ];

            if ($result->isSuccess() && $result->getResponse() !== null) {
                try {
                    $data['response'] = $result->getResponse()->json();
                } catch (\JsonException) {
                    $data['response'] = ['body' => $result->getResponse()->getBody()];
                }
            }

            if ($result->isFailure() && $result->getException() !== null) {
                $data['error'] = $result->getException()->getMessage();
            }

            return $data;
        }, $this->results);
    }
}
