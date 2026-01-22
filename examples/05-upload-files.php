<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Freelo\Sdk\Auth\ApiKeyCredentials;
use Freelo\Sdk\Freelo;
use Freelo\Sdk\Exception\ApiException;

/**
 * Example: Upload and Download Files
 *
 * This example demonstrates how to upload and download files in Freelo.
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

    echo "Uploading a file...\n\n";

    // Create a temporary test file
    $tempFile = sys_get_temp_dir() . '/freelo-test-' . uniqid() . '.txt';
    file_put_contents($tempFile, "This is a test file for Freelo SDK\nCreated at: " . date('Y-m-d H:i:s'));

    // Upload the file
    $file = $freelo->files()->upload($projectId, $tempFile);

    echo "✓ File uploaded successfully!\n\n";
    echo "File ID: {$file->id}\n";
    echo "Name: {$file->name}\n";
    echo "Size: {$file->size} bytes\n";
    if ($file->mimeType) {
        echo "MIME Type: {$file->mimeType}\n";
    }

    // Get file details
    echo "\nFetching file details...\n";
    $fileDetails = $freelo->files()->get($file->id);
    echo "✓ File name: {$fileDetails->name}\n";

    // Download the file
    echo "\nDownloading file...\n";
    $downloadPath = sys_get_temp_dir() . '/freelo-download-' . uniqid() . '.txt';
    $freelo->files()->download($file->id, $downloadPath);
    echo "✓ File downloaded to: {$downloadPath}\n";

    // Verify download
    if (file_exists($downloadPath)) {
        echo "Downloaded file size: " . filesize($downloadPath) . " bytes\n";
    }

    // Delete the file
    echo "\nDeleting file...\n";
    $deleted = $freelo->files()->delete($file->id);
    if ($deleted) {
        echo "✓ File deleted successfully\n";
    }

    // Cleanup temporary files
    @unlink($tempFile);
    @unlink($downloadPath);

    echo "\n✓ Example completed successfully!\n";
} catch (ApiException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
