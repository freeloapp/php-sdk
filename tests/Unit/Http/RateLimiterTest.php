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

    public function testParsesStructuredRateLimitHeader(): void
    {
        $rateLimiter = new RateLimiter();
        $before = time();
        $rateLimiter->updateFromHeaders(['RateLimit' => '"default";r=42;t=5']);

        $this->assertSame(42, $rateLimiter->getRemaining());
        $this->assertNull($rateLimiter->getLimit());
        $this->assertGreaterThanOrEqual($before + 5, $rateLimiter->getResetAt());
        $this->assertLessThanOrEqual(time() + 5, $rateLimiter->getResetAt());
        $this->assertFalse($rateLimiter->isLimitExceeded());
    }

    public function testParsesStructuredRateLimitWithPolicy(): void
    {
        $rateLimiter = new RateLimiter();
        $rateLimiter->updateFromHeaders([
            'ratelimit-policy' => ['"default";q=60;w=20'],
            'ratelimit' => ['"default";r=0;t=3'],
        ]);

        $this->assertSame(60, $rateLimiter->getLimit());
        $this->assertSame(0, $rateLimiter->getRemaining());
        $this->assertTrue($rateLimiter->isLimitExceeded());
        $this->assertGreaterThanOrEqual(2, $rateLimiter->getSecondsUntilReset());
        $this->assertLessThanOrEqual(3, $rateLimiter->getSecondsUntilReset());
    }

    public function testStructuredParamsOrderAndWhitespaceDoNotMatter(): void
    {
        $rateLimiter = new RateLimiter();
        $rateLimiter->updateFromHeaders([
            'RateLimit-Policy' => '"default"; w=20; q=60',
            'RateLimit' => '"default"; t=5; r=7',
        ]);

        $this->assertSame(60, $rateLimiter->getLimit());
        $this->assertSame(7, $rateLimiter->getRemaining());
    }

    public function testMultipleStructuredPoliciesUseMostRestrictive(): void
    {
        $rateLimiter = new RateLimiter();
        $rateLimiter->updateFromHeaders([
            'RateLimit-Policy' => '"hour";q=1000;w=3600, "burst";q=10;w=1',
            'RateLimit' => '"hour";r=900;t=3000, "burst";r=2;t=1',
        ]);

        $this->assertSame(10, $rateLimiter->getLimit());
        $this->assertSame(2, $rateLimiter->getRemaining());
    }

    public function testPolicyAloneProvidesLimit(): void
    {
        $rateLimiter = new RateLimiter();
        $rateLimiter->updateFromHeaders(['RateLimit-Policy' => '"default";q=60;w=20']);

        $this->assertSame(60, $rateLimiter->getLimit());
        $this->assertNull($rateLimiter->getRemaining());
        $this->assertNull($rateLimiter->getResetAt());
    }

    public function testStructuredHeaderTakesPrecedenceOverLegacy(): void
    {
        $rateLimiter = new RateLimiter();
        $rateLimiter->updateFromHeaders([
            'RateLimit' => '"default";r=5;t=10',
            'X-RateLimit-Remaining' => '99',
            'X-RateLimit-Limit' => '100',
        ]);

        $this->assertSame(5, $rateLimiter->getRemaining());
        $this->assertSame(100, $rateLimiter->getLimit());
    }

    public function testMissingHeadersLeaveValuesNull(): void
    {
        $rateLimiter = new RateLimiter();
        $rateLimiter->updateFromHeaders(['RateLimit' => '"default";r=1;t=1']);
        $rateLimiter->updateFromHeaders(['Content-Type' => 'application/json']);

        $this->assertNull($rateLimiter->getLimit());
        $this->assertNull($rateLimiter->getRemaining());
        $this->assertNull($rateLimiter->getResetAt());
        $this->assertFalse($rateLimiter->isLimitExceeded());
        $this->assertSame(0, $rateLimiter->calculateDelay());
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function invalidStructuredHeadersProvider(): array
    {
        return [
            'negative values' => ['"default";r=-1;t=-5', '"default";q=-60;w=20'],
            'non-integer values' => ['"default";r=abc;t=1.5', '"default";q=6e1;w=20'],
            'missing values' => ['"default";r;t=', '"default";q;w=20'],
            'empty header' => ['', ''],
            'garbage' => [';;;,,,', '=,;'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('invalidStructuredHeadersProvider')]
    public function testInvalidStructuredValuesAreIgnored(string $rateLimit, string $policy): void
    {
        $rateLimiter = new RateLimiter();
        $rateLimiter->updateFromHeaders([
            'RateLimit' => $rateLimit,
            'RateLimit-Policy' => $policy,
        ]);

        $this->assertNull($rateLimiter->getLimit());
        $this->assertNull($rateLimiter->getRemaining());
        $this->assertNull($rateLimiter->getResetAt());
        $this->assertFalse($rateLimiter->isLimitExceeded());
    }
}
