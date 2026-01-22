<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Freelo\Sdk\Auth\ApiKeyCredentials;
use Freelo\Sdk\Freelo;

/**
 * Example: Basic Authentication
 *
 * This example demonstrates how to initialize the Freelo SDK with API key credentials.
 */

// Create credentials with your API key and email
$credentials = new ApiKeyCredentials(
    apiKey: 'your-api-key-here',
    email: 'your-email@example.com'
);

// Initialize the SDK
$freelo = new Freelo($credentials);

// Optional: Customize configuration
$freelo
    ->setUserAgent('My-App/1.0')
    ->setApiUrl('https://api.freelo.cz/v1'); // Use custom API URL if needed

echo "✓ Freelo SDK initialized successfully!\n";
echo "  API URL: " . $freelo->getApiUrl() . "\n";
echo "  User Agent: " . $freelo->getUserAgent() . "\n";
