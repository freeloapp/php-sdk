<?php

declare(strict_types=1);

namespace Freelo\Sdk\Webhook\Event;

/**
 * Base class for all webhook events
 */
abstract class WebhookEvent
{
    /**
     * @param array<string, mixed> $data The raw event data
     */
    public function __construct(
        protected readonly array $data,
    ) {
    }

    /**
     * Get the event type
     */
    abstract public function getType(): string;

    /**
     * Get the raw event data
     *
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get a specific field from the event data
     *
     * @param string $key The field name
     * @param mixed $default Default value if field doesn't exist
     * @return mixed The field value
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Check if a field exists in the event data
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }
}
