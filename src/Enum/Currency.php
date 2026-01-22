<?php

declare(strict_types=1);

namespace Freelo\Sdk\Enum;

/**
 * Supported currencies in Freelo
 */
enum Currency: string
{
    case EUR = 'EUR';
    case USD = 'USD';
    case CZK = 'CZK';
}
