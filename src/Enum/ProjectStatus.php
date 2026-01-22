<?php

declare(strict_types=1);

namespace Freelo\Sdk\Enum;

/**
 * Project status values
 */
enum ProjectStatus: string
{
    case Active = 'active';
    case Archived = 'archived';
    case Template = 'template';
}
