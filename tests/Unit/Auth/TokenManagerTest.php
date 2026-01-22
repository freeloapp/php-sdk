<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Auth;

use Freelo\Sdk\Auth\TokenManager;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;

class TokenManagerTest extends TestCase
{
    public function testGetTokenWithoutCache(): void
    {
        $manager = new TokenManager();
        $generator = fn() => 'generated-token';

        $token = $manager->getToken('test-key', $generator);

        $this->assertSame('generated-token', $token);
    }

    public function testGetTokenWithCacheHit(): void
    {
        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->once())
            ->method('get')
            ->with('test-key')
            ->willReturn('cached-token');

        $cache->expects($this->never())
            ->method('set');

        $manager = new TokenManager($cache);
        $generator = fn() => 'generated-token';

        $token = $manager->getToken('test-key', $generator);

        $this->assertSame('cached-token', $token);
    }

    public function testGetTokenWithCacheMiss(): void
    {
        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->once())
            ->method('get')
            ->with('test-key')
            ->willReturn(null);

        $cache->expects($this->once())
            ->method('set')
            ->with('test-key', 'generated-token', 3600);

        $manager = new TokenManager($cache);
        $generator = fn() => 'generated-token';

        $token = $manager->getToken('test-key', $generator);

        $this->assertSame('generated-token', $token);
    }

    public function testGetTokenWithCustomTtl(): void
    {
        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->once())
            ->method('get')
            ->willReturn(null);

        $cache->expects($this->once())
            ->method('set')
            ->with('test-key', 'generated-token', 7200);

        $manager = new TokenManager($cache, 7200);
        $generator = fn() => 'generated-token';

        $manager->getToken('test-key', $generator);
    }

    public function testGetTokenWithNonStringCachedValue(): void
    {
        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->once())
            ->method('get')
            ->willReturn(12345); // Non-string value

        $cache->expects($this->once())
            ->method('set')
            ->with('test-key', 'generated-token', 3600);

        $manager = new TokenManager($cache);
        $generator = fn() => 'generated-token';

        $token = $manager->getToken('test-key', $generator);

        $this->assertSame('generated-token', $token);
    }

    public function testInvalidateTokenWithCache(): void
    {
        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->once())
            ->method('delete')
            ->with('test-key');

        $manager = new TokenManager($cache);
        $manager->invalidateToken('test-key');
    }

    public function testInvalidateTokenWithoutCache(): void
    {
        $manager = new TokenManager();

        // Should not throw exception even without cache
        $manager->invalidateToken('test-key');

        $this->assertTrue(true); // If we get here, no exception was thrown
    }

    public function testClearAllWithCache(): void
    {
        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->once())
            ->method('clear');

        $manager = new TokenManager($cache);
        $manager->clearAll();
    }

    public function testClearAllWithoutCache(): void
    {
        $manager = new TokenManager();

        // Should not throw exception even without cache
        $manager->clearAll();

        $this->assertTrue(true); // If we get here, no exception was thrown
    }
}
