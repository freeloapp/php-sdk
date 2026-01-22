<?php

declare(strict_types=1);

namespace Freelo\Sdk\Auth;

/**
 * Interface for authentication credentials
 */
interface Credentials
{
    /**
     * Get authentication headers to include in API requests
     *
     * @return array<string, string>
     */
    public function getAuthHeaders(): array;
}
