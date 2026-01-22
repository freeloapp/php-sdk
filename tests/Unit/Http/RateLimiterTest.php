<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Http;

use Freelo\Sdk\Exception\RateLimitException;
use Freelo\Sdk\Http\RateLimiter;
use PHPUnit\Framework\TestCase;

class RateLimiterTest extends TestCase
{
    public function testUpdateFromHeaders(): void
    {
        $rateLimiter = new RateLimiter();
        $headers = [
            'X-RateLimit-Limit' => '100',
            'X-RateLimit-Remaining' => '50',
            'X-RateLimit-Reset' => (string)(time() + 3600),
        ];

        $rateLimiter->updateFromHeaders($headers);

        $this->assertEquals(100, $rateLimiter->getLimit());
        $this->assertEquals(50, $rateLimiter->getRemaining());
        $this->assertNotNull($rateLimiter->getResetAt());
    }

    public function testUpdateFromHeadersWithDifferentFormat(): void
    {
        $rateLimiter = new RateLimiter();
        $headers = [
            'RateLimit-Limit' => '200',
            'RateLimit-Remaining' => '150',
            'RateLimit-Reset' => (string)(time() + 1800),
        ];

        $rateLimiter->updateFromHeaders($headers);

        $this->assertEquals(200, $rateLimiter->getLimit());
        $this->assertEquals(150, $rateLimiter->getRemaining());
    }

    public function testIsLimitExceeded(): void
    {
        $rateLimiter = new RateLimiter();

        $headers = [
            'X-RateLimit-Limit' => '100',
            'X-RateLimit-Remaining' => '0',
            'X-RateLimit-Reset' => (string)(time() + 60),
        ];
        $rateLimiter->updateFromHeaders($headers);

        $this->assertTrue($rateLimiter->isLimitExceeded());
    }

    public function testIsNotLimitExceeded(): void
    {
        $rateLimiter = new RateLimiter();

        $headers = [
            'X-RateLimit-Limit' => '100',
            'X-RateLimit-Remaining' => '50',
            'X-RateLimit-Reset' => (string)(time() + 60),
        ];
        $rateLimiter->updateFromHeaders($headers);

        $this->assertFalse($rateLimiter->isLimitExceeded());
    }

    public function testGetSecondsUntilReset(): void
    {
        $rateLimiter = new RateLimiter();
        $resetTime = time() + 120; // 2 minutes from now

        $headers = [
            'X-RateLimit-Limit' => '100',
            'X-RateLimit-Remaining' => '50',
            'X-RateLimit-Reset' => (string)$resetTime,
        ];
        $rateLimiter->updateFromHeaders($headers);

        $seconds = $rateLimiter->getSecondsUntilReset();
        $this->assertGreaterThan(0, $seconds);
        $this->assertLessThanOrEqual(120, $seconds);
    }

    public function testShouldDelay(): void
    {
        $rateLimiter = new RateLimiter();

        $headers = [
            'X-RateLimit-Limit' => '100',
            'X-RateLimit-Remaining' => '3',
            'X-RateLimit-Reset' => (string)(time() + 60),
        ];
        $rateLimiter->updateFromHeaders($headers);

        $this->assertTrue($rateLimiter->shouldDelay(5));
        $this->assertFalse($rateLimiter->shouldDelay(2));
    }

    public function testCalculateDelay(): void
    {
        $rateLimiter = new RateLimiter();

        $headers = [
            'X-RateLimit-Limit' => '100',
            'X-RateLimit-Remaining' => '90',
            'X-RateLimit-Reset' => (string)(time() + 60),
        ];
        $rateLimiter->updateFromHeaders($headers);

        $delay = $rateLimiter->calculateDelay();
        $this->assertGreaterThanOrEqual(0, $delay);
        $this->assertLessThanOrEqual(5, $delay);
    }

    public function testThrowIfExceeded(): void
    {
        $this->expectException(RateLimitException::class);

        $rateLimiter = new RateLimiter();
        $headers = [
            'X-RateLimit-Limit' => '100',
            'X-RateLimit-Remaining' => '0',
            'X-RateLimit-Reset' => (string)(time() + 60),
        ];
        $rateLimiter->updateFromHeaders($headers);

        $rateLimiter->throwIfExceeded();
    }

    public function testDoesNotThrowIfNotExceeded(): void
    {
        $rateLimiter = new RateLimiter();
        $headers = [
            'X-RateLimit-Limit' => '100',
            'X-RateLimit-Remaining' => '50',
            'X-RateLimit-Reset' => (string)(time() + 60),
        ];
        $rateLimiter->updateFromHeaders($headers);

        $rateLimiter->throwIfExceeded();
        $this->assertTrue(true); // No exception thrown
    }

    public function testReset(): void
    {
        $rateLimiter = new RateLimiter();
        $headers = [
            'X-RateLimit-Limit' => '100',
            'X-RateLimit-Remaining' => '50',
            'X-RateLimit-Reset' => (string)(time() + 60),
        ];
        $rateLimiter->updateFromHeaders($headers);

        $rateLimiter->reset();

        $this->assertNull($rateLimiter->getLimit());
        $this->assertNull($rateLimiter->getRemaining());
        $this->assertNull($rateLimiter->getResetAt());
    }
}
