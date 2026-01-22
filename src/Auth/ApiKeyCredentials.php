<?php

declare(strict_types=1);

namespace Freelo\Sdk\Auth;

use InvalidArgumentException;

/**
 * API Key authentication credentials for Freelo API
 *
 * Requires both an API key and email address for authentication.
 */
class ApiKeyCredentials implements Credentials
{
    private string $apiKey;
    private string $email;

    public function __construct(string $apiKey, string $email)
    {
        if (empty($apiKey)) {
            throw new InvalidArgumentException('API key cannot be empty');
        }

        if (empty($email)) {
            throw new InvalidArgumentException('Email cannot be empty');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }

        $this->apiKey = $apiKey;
        $this->email = $email;
    }

    /**
     * Get authentication headers using HTTP Basic Auth
     *
     * The Freelo API uses HTTP Basic Authentication where:
     * - Username: your email address
     * - Password: your API key
     *
     * @return array<string, string>
     */
    public function getAuthHeaders(): array
    {
        $credentials = base64_encode($this->email . ':' . $this->apiKey);

        return [
            'Authorization' => 'Basic ' . $credentials,
        ];
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
