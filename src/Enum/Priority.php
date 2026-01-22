<?php

declare(strict_types=1);

namespace Freelo\Sdk\Enum;

/**
 * Task priority levels
 *
 * API uses string values: 'l' (low), 'm' (medium), 'h' (high)
 */
enum Priority: string
{
    case Low = 'l';
    case Medium = 'm';
    case High = 'h';

    /**
     * Get human-readable label for the priority
     */
    public function label(): string
    {
        return match ($this) {
            self::Low => 'Low',
            self::Medium => 'Medium',
            self::High => 'High',
        };
    }
}
