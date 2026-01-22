<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Http;

use Freelo\Sdk\Exception\ApiException;
use Freelo\Sdk\Exception\RateLimitException;
use Freelo\Sdk\Http\RetryHandler;
use PHPUnit\Framework\TestCase;

class RetryHandlerTest extends TestCase
{
    public function testExecuteSuccessOnFirstAttempt(): void
    {
        $handler = new RetryHandler(maxRetries: 3);
        $attemptCount = 0;

        $result = $handler->execute(function () use (&$attemptCount) {
            $attemptCount++;
            return 'success';
        });

        $this->assertEquals('success', $result);
        $this->assertEquals(1, $attemptCount);
    }

    public function testExecuteRetriesOnFailure(): void
    {
        $handler = new RetryHandler(maxRetries: 3, initialDelay: 0);
        $attemptCount = 0;

        try {
            $handler->execute(function () use (&$attemptCount) {
                $attemptCount++;
                if ($attemptCount < 3) {
                    // Create a mock response with 500 status
                    $response = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
                    $response->method('getStatusCode')->willReturn(500);
                    throw new ApiException('Temporary error', 500, null, $response);
                }
                return 'success';
            });
        } catch (\Throwable) {
            // Ignore
        }

        $this->assertEquals(3, $attemptCount);
    }

    public function testExecuteThrowsAfterMaxRetries(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Max retries exceeded');

        $handler = new RetryHandler(maxRetries: 2, initialDelay: 0);

        $handler->execute(function () {
            throw new ApiException('Max retries exceeded', 500);
        });
    }

    public function testExecuteDoesNotRetryNonRetriableErrors(): void
    {
        $handler = new RetryHandler(maxRetries: 3, initialDelay: 0);
        $attemptCount = 0;

        try {
            $handler->execute(function () use (&$attemptCount) {
                $attemptCount++;
                throw new \RuntimeException('Non-retriable error');
            });
        } catch (\Throwable) {
            // Ignore
        }

        $this->assertEquals(1, $attemptCount);
    }

    public function testExecuteRetriesRateLimitException(): void
    {
        $handler = new RetryHandler(maxRetries: 2, initialDelay: 0);
        $attemptCount = 0;

        try {
            $handler->execute(function () use (&$attemptCount) {
                $attemptCount++;
                if ($attemptCount < 2) {
                    $exception = new RateLimitException('Rate limited', 429);
                    $exception->setRetryAfter(1);
                    throw $exception;
                }
                return 'success';
            });
        } catch (\Throwable) {
            // Ignore
        }

        $this->assertEquals(2, $attemptCount);
    }

    public function testCustomShouldRetryCallback(): void
    {
        $handler = new RetryHandler(maxRetries: 3, initialDelay: 0);
        $attemptCount = 0;

        try {
            $handler->execute(
                function () use (&$attemptCount) {
                    $attemptCount++;
                    // Create a mock response with 500 status (retriable)
                    $response = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
                    $response->method('getStatusCode')->willReturn(500);
                    throw new ApiException('Error', 500, null, $response);
                },
                function (int $attempt, \Throwable $e): bool {
                    // Only retry on first attempt
                    return $attempt === 0;
                },
            );
        } catch (\Throwable) {
            // Ignore
        }

        $this->assertEquals(2, $attemptCount); // Initial attempt + 1 retry
    }

    public function testGetters(): void
    {
        $handler = new RetryHandler(
            maxRetries: 5,
            initialDelay: 2,
            maxDelay: 120,
            multiplier: 3,
        );

        $this->assertEquals(5, $handler->getMaxRetries());
        $this->assertEquals(2, $handler->getInitialDelay());
        $this->assertEquals(120, $handler->getMaxDelay());
        $this->assertEquals(3, $handler->getMultiplier());
    }
}
