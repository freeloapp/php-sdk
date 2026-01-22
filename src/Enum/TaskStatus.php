<?php

declare(strict_types=1);

namespace Freelo\Sdk\Enum;

/**
 * Task status values
 */
enum TaskStatus: string
{
    case Todo = 'todo';
    case InProgress = 'in_progress';
    case Done = 'done';
    case Cancelled = 'cancelled';
}
