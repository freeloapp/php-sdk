<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Integration;

use Freelo\Sdk\Auth\ApiKeyCredentials;
use Freelo\Sdk\Freelo;
use PHPUnit\Framework\TestCase;

/**
 * Integration tests for Freelo SDK
 *
 * These tests require real API credentials. Set the following environment variables:
 * - FREELO_API_KEY: Your Freelo API key
 * - FREELO_EMAIL: Your Freelo email
 *
 * Tests will be skipped if credentials are not provided.
 */
class FreeloIntegrationTest extends TestCase
{
    private ?Freelo $freelo = null;

    protected function setUp(): void
    {
        $apiKey = getenv('FREELO_API_KEY');
        $email = getenv('FREELO_EMAIL');

        if ($apiKey === false || $email === false || $apiKey === '' || $email === '') {
            $this->markTestSkipped(
                'Integration tests require FREELO_API_KEY and FREELO_EMAIL environment variables'
            );
        }

        $credentials = new ApiKeyCredentials($apiKey, $email);
        $this->freelo = new Freelo($credentials);
    }

    public function testCanListProjects(): void
    {
        if ($this->freelo === null) {
            $this->markTestSkipped('Freelo client not initialized');
        }

        $projects = $this->freelo->projects()->list();

        $this->assertIsArray($projects);
        // Note: We don't assert count > 0 as the account might have no projects
    }

    public function testCanGetOwnedProjects(): void
    {
        if ($this->freelo === null) {
            $this->markTestSkipped('Freelo client not initialized');
        }

        $projects = $this->freelo->projects()->getOwned();

        $this->assertIsArray($projects);
    }

    public function testCanSetCustomApiUrl(): void
    {
        if ($this->freelo === null) {
            $this->markTestSkipped('Freelo client not initialized');
        }

        $customUrl = 'https://api.freelo.cz/v1';
        $this->freelo->setApiUrl($customUrl);

        $this->assertSame($customUrl, $this->freelo->getApiUrl());
    }

    public function testCanSetCustomUserAgent(): void
    {
        if ($this->freelo === null) {
            $this->markTestSkipped('Freelo client not initialized');
        }

        $customAgent = 'My-Custom-Agent/1.0';
        $this->freelo->setUserAgent($customAgent);

        $this->assertSame($customAgent, $this->freelo->getUserAgent());
    }
}
