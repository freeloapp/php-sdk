<?php

declare(strict_types=1);

namespace Freelo\Sdk\Exception;

/**
 * Exception thrown when rate limit is exceeded
 */
class RateLimitException extends ApiException
{
    private ?int $retryAfter = null;

    public function setRetryAfter(int $seconds): void
    {
        $this->retryAfter = $seconds;
    }

    public function getRetryAfter(): ?int
    {
        return $this->retryAfter;
    }
}
