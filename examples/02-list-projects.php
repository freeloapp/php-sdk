<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Freelo\Sdk\Auth\ApiKeyCredentials;
use Freelo\Sdk\Freelo;
use Freelo\Sdk\Exception\ApiException;

/**
 * Example: List Projects
 *
 * This example demonstrates how to list all projects in your Freelo account.
 */

// Initialize SDK
$credentials = new ApiKeyCredentials(
    apiKey: getenv('FREELO_API_KEY') ?: 'your-api-key',
    email: getenv('FREELO_EMAIL') ?: 'your-email@example.com'
);

$freelo = new Freelo($credentials);

try {
    // List all projects
    echo "Fetching all projects...\n\n";
    $projects = $freelo->projects()->list();

    echo "Found " . count($projects) . " projects:\n\n";

    foreach ($projects as $project) {
        echo "- {$project->name}\n";
        echo "  ID: {$project->id}\n";
        if ($project->description) {
            echo "  Description: {$project->description}\n";
        }
        if ($project->currency) {
            echo "  Currency: {$project->currency}\n";
        }
        echo "\n";
    }

    // Get owned projects only
    echo "\nFetching owned projects...\n\n";
    $ownedProjects = $freelo->projects()->getOwned();
    echo "You own " . count($ownedProjects) . " projects\n";

    // Get invited projects
    echo "\nFetching invited projects...\n\n";
    $invitedProjects = $freelo->projects()->getInvited();
    echo "You're invited to " . count($invitedProjects) . " projects\n";
} catch (ApiException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
