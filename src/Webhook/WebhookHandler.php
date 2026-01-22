<?php

declare(strict_types=1);

namespace Freelo\Sdk\Webhook;

use Freelo\Sdk\Exception\WebhookException;
use Freelo\Sdk\Webhook\Event\WebhookEvent;
use Freelo\Sdk\Webhook\Event\TaskCreatedEvent;
use Freelo\Sdk\Webhook\Event\TaskUpdatedEvent;
use Freelo\Sdk\Webhook\Event\CommentAddedEvent;
use Freelo\Sdk\Webhook\Event\ProjectUpdatedEvent;

/**
 * Handles incoming webhook requests from Freelo API
 *
 * Provides signature verification and payload parsing for webhook events.
 */
class WebhookHandler
{
    /**
     * @param string|null $secret Webhook secret for signature verification
     */
    public function __construct(
        private readonly ?string $secret = null,
    ) {
    }

    /**
     * Handle an incoming webhook request
     *
     * @param string $payload The raw request body
     * @param string|null $signature The signature header value (e.g., X-Freelo-Signature)
     * @return WebhookEvent The parsed webhook event
     * @throws WebhookException If signature verification fails or payload is invalid
     */
    public function handle(string $payload, ?string $signature = null): WebhookEvent
    {
        // Verify signature if secret is configured
        if ($this->secret !== null && $signature !== null) {
            $this->verifySignature($payload, $signature);
        }

        // Parse the payload
        $data = $this->parsePayload($payload);

        // Create and return the appropriate event
        return $this->createEvent($data);
    }

    /**
     * Verify the webhook signature
     *
     * @param string $payload The raw request body
     * @param string $signature The signature to verify
     * @throws WebhookException If signature is invalid
     */
    private function verifySignature(string $payload, string $signature): void
    {
        if ($this->secret === null) {
            throw new WebhookException('Webhook secret is not configured');
        }

        $expectedSignature = hash_hmac('sha256', $payload, $this->secret);

        if (!hash_equals($expectedSignature, $signature)) {
            throw new WebhookException('Invalid webhook signature');
        }
    }

    /**
     * Parse the JSON payload
     *
     * @param string $payload The raw request body
     * @return array<string, mixed> The parsed data
     * @throws WebhookException If payload is not valid JSON
     */
    private function parsePayload(string $payload): array
    {
        try {
            $data = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new WebhookException('Invalid JSON payload: ' . $e->getMessage(), 0, $e);
        }

        if (!is_array($data)) {
            throw new WebhookException('Payload must be a JSON object');
        }

        return $data;
    }

    /**
     * Create the appropriate event object based on the payload
     *
     * @param array<string, mixed> $data The parsed payload data
     * @return WebhookEvent The created event
     * @throws WebhookException If event type is unknown or required fields are missing
     */
    private function createEvent(array $data): WebhookEvent
    {
        if (!isset($data['event'])) {
            throw new WebhookException('Missing "event" field in payload');
        }

        if (!isset($data['data'])) {
            throw new WebhookException('Missing "data" field in payload');
        }

        $eventType = $data['event'];
        $eventData = $data['data'];

        return match ($eventType) {
            'task.created' => new TaskCreatedEvent($eventData),
            'task.updated' => new TaskUpdatedEvent($eventData),
            'comment.added' => new CommentAddedEvent($eventData),
            'project.updated' => new ProjectUpdatedEvent($eventData),
            default => throw new WebhookException('Unknown event type: ' . $eventType),
        };
    }

    /**
     * Create a webhook handler from request data
     *
     * This is a convenience method for handling webhooks from PSR-7 ServerRequestInterface
     *
     * @param string $payload The raw request body
     * @param array<string, string> $headers Request headers
     * @param string|null $secret Webhook secret for signature verification
     * @param string $signatureHeader The header name containing the signature (default: X-Freelo-Signature)
     * @return WebhookEvent The parsed webhook event
     * @throws WebhookException If signature verification fails or payload is invalid
     */
    public static function handleRequest(
        string $payload,
        array $headers,
        ?string $secret = null,
        string $signatureHeader = 'X-Freelo-Signature',
    ): WebhookEvent {
        $handler = new self($secret);

        // Extract signature from headers (case-insensitive)
        $signature = null;
        $signatureHeaderLower = strtolower($signatureHeader);
        foreach ($headers as $name => $value) {
            if (strtolower($name) === $signatureHeaderLower) {
                /** @var string|string[] $value */
                $signature = is_array($value) ? ($value[0] ?? null) : $value;
                break;
            }
        }

        return $handler->handle($payload, $signature);
    }
}
