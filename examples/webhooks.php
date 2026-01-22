<?php

declare(strict_types=1);

/**
 * Example: Handling Freelo Webhooks
 *
 * This example demonstrates how to receive and process webhook events from Freelo.
 */

require __DIR__ . '/../vendor/autoload.php';

use Freelo\Sdk\Webhook\WebhookHandler;
use Freelo\Sdk\Exception\WebhookException;

// Your webhook secret (configured in Freelo settings)
$webhookSecret = getenv('FREELO_WEBHOOK_SECRET') ?: 'your-webhook-secret';

// Initialize webhook handler
$handler = new WebhookHandler($webhookSecret);

// Get the raw request body and signature
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_FREELO_SIGNATURE'] ?? null;

try {
    // Handle the incoming webhook
    $event = $handler->handle($payload, $signature);

    echo "Received webhook event: " . $event->getType() . "\n";

    // Process based on event type
    switch ($event->getType()) {
        case 'task.created':
            echo "New task created:\n";
            echo "  ID: " . $event->getTaskId() . "\n";
            echo "  Name: " . $event->getTaskName() . "\n";
            echo "  Project ID: " . $event->getProjectId() . "\n";
            echo "  Creator ID: " . $event->getCreatorId() . "\n";
            echo "  Created At: " . $event->getCreatedAt() . "\n";
            break;

        case 'task.updated':
            echo "Task updated:\n";
            echo "  ID: " . $event->getTaskId() . "\n";
            echo "  Name: " . $event->getTaskName() . "\n";
            echo "  Updated fields: " . implode(', ', $event->getUpdatedFields() ?? []) . "\n";
            echo "  Updater ID: " . $event->getUpdaterId() . "\n";
            break;

        case 'comment.added':
            echo "Comment added:\n";
            echo "  Comment ID: " . $event->getCommentId() . "\n";
            echo "  Task ID: " . $event->getTaskId() . "\n";
            echo "  Content: " . $event->getContent() . "\n";
            echo "  Author ID: " . $event->getAuthorId() . "\n";
            break;

        case 'project.updated':
            echo "Project updated:\n";
            echo "  Project ID: " . $event->getProjectId() . "\n";
            echo "  Name: " . $event->getProjectName() . "\n";
            echo "  Updated fields: " . implode(', ', $event->getUpdatedFields() ?? []) . "\n";
            break;

        default:
            echo "Unknown event type: " . $event->getType() . "\n";
    }

    // Access raw data if needed
    $rawData = $event->getData();
    echo "\nRaw event data:\n";
    print_r($rawData);

    // Send success response
    http_response_code(200);
    echo "Webhook processed successfully\n";

} catch (WebhookException $e) {
    // Invalid signature or malformed payload
    http_response_code(400);
    echo "Webhook error: " . $e->getMessage() . "\n";
    error_log("Webhook validation failed: " . $e->getMessage());
}
