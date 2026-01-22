<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Integration;

use Freelo\Sdk\Auth\ApiKeyCredentials;
use Freelo\Sdk\Freelo;
use PHPUnit\Framework\TestCase;

/**
 * Base class for integration tests
 *
 * These tests require real API credentials. Set the following environment variables:
 * - FREELO_API_KEY: Your Freelo API key
 * - FREELO_EMAIL: Your Freelo email
 *
 * You can configure these in phpunit.xml or set them in your shell:
 *
 * ```bash
 * export FREELO_API_KEY="your-api-key"
 * export FREELO_EMAIL="your@email.com"
 * ./vendor/bin/phpunit --testsuite "Integration Tests"
 * ```
 *
 * Tests will be skipped if credentials are not provided.
 */
abstract class IntegrationTestCase extends TestCase
{
    protected ?Freelo $freelo = null;

    protected function setUp(): void
    {
        parent::setUp();

        if (!$this->hasApiCredentials()) {
            $this->markTestSkipped(
                'Integration tests require FREELO_API_KEY and FREELO_EMAIL environment variables'
            );
        }

        $credentials = new ApiKeyCredentials(
            (string) getenv('FREELO_API_KEY'),
            (string) getenv('FREELO_EMAIL')
        );

        $this->freelo = new Freelo($credentials);
    }

    protected function hasApiCredentials(): bool
    {
        $apiKey = getenv('FREELO_API_KEY');
        $email = getenv('FREELO_EMAIL');

        return $apiKey !== false
            && $email !== false
            && $apiKey !== ''
            && $email !== '';
    }

    /**
     * Helper to ensure freelo client is available
     */
    protected function getFreelo(): Freelo
    {
        if ($this->freelo === null) {
            $this->markTestSkipped('Freelo client not initialized');
        }

        return $this->freelo;
    }
}
