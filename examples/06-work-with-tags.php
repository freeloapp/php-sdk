<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Freelo\Sdk\Auth\ApiKeyCredentials;
use Freelo\Sdk\Freelo;
use Freelo\Sdk\Exception\ApiException;

/**
 * Example: Work with Tags
 *
 * This example demonstrates how to create and manage tags in Freelo.
 */

// Initialize SDK
$credentials = new ApiKeyCredentials(
    apiKey: getenv('FREELO_API_KEY') ?: 'your-api-key',
    email: getenv('FREELO_EMAIL') ?: 'your-email@example.com'
);

$freelo = new Freelo($credentials);

try {
    // Replace with your actual project ID
    $projectId = 'your-project-id';

    echo "Fetching tags...\n\n";

    // List all tags in the project
    $tags = $freelo->tags()->list($projectId);
    echo "Found " . count($tags) . " tags\n\n";

    foreach ($tags as $tag) {
        echo "- {$tag->name}\n";
        echo "  ID: {$tag->id}\n";
        if ($tag->color) {
            echo "  Color: {$tag->color}\n";
        }
        echo "\n";
    }

    // Create a new tag
    echo "Creating a new tag...\n";
    $newTag = $freelo->tags()->create($projectId, [
        'name' => 'Important',
        'color' => '#FF0000',
    ]);

    echo "✓ Tag created successfully!\n\n";
    echo "Tag ID: {$newTag->id}\n";
    echo "Name: {$newTag->name}\n";
    echo "Color: {$newTag->color}\n";

    // Get a specific tag
    echo "\nFetching tag details...\n";
    $tag = $freelo->tags()->get($newTag->id);
    echo "✓ Retrieved: {$tag->name}\n";

    // Update the tag
    echo "\nUpdating tag...\n";
    $updatedTag = $freelo->tags()->update($newTag->id, [
        'name' => 'Critical',
        'color' => '#CC0000',
    ]);
    echo "✓ Updated to: {$updatedTag->name} ({$updatedTag->color})\n";

    // Delete the tag (cleanup)
    echo "\nDeleting tag...\n";
    $deleted = $freelo->tags()->delete($newTag->id);
    if ($deleted) {
        echo "✓ Tag deleted successfully\n";
    }

    echo "\n✓ Example completed successfully!\n";
} catch (ApiException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
