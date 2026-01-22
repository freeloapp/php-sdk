<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Auth;

use Freelo\Sdk\Auth\ApiKeyCredentials;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ApiKeyCredentialsTest extends TestCase
{
    public function testConstructorWithValidCredentials(): void
    {
        $credentials = new ApiKeyCredentials('test-api-key', 'test@example.com');

        $this->assertSame('test-api-key', $credentials->getApiKey());
        $this->assertSame('test@example.com', $credentials->getEmail());
    }

    public function testGetAuthHeaders(): void
    {
        $credentials = new ApiKeyCredentials('test-api-key', 'test@example.com');
        $headers = $credentials->getAuthHeaders();

        $this->assertIsArray($headers);
        $this->assertArrayHasKey('Authorization', $headers);

        // Verify HTTP Basic Auth format: "Basic base64(email:apikey)"
        $expectedCredentials = base64_encode('test@example.com:test-api-key');
        $this->assertSame('Basic ' . $expectedCredentials, $headers['Authorization']);
    }

    public function testConstructorThrowsExceptionForEmptyApiKey(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('API key cannot be empty');

        new ApiKeyCredentials('', 'test@example.com');
    }

    public function testConstructorThrowsExceptionForEmptyEmail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Email cannot be empty');

        new ApiKeyCredentials('test-api-key', '');
    }

    public function testConstructorThrowsExceptionForInvalidEmail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format');

        new ApiKeyCredentials('test-api-key', 'invalid-email');
    }
}
