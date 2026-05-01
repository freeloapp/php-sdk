<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Support;

use DateTimeImmutable;
use DateTimeZone;

final class PragueTime
{
    /**
     * Build a UTC \DateTimeImmutable from a naive string interpreted as Europe/Prague.
     *
     * Mirrors what DateTimeParser::parseDateTime() does for naive datetime strings
     * coming back from the V1 API — useful when comparing against parsed values.
     */
    public static function utc(string $naive): DateTimeImmutable
    {
        return (new DateTimeImmutable($naive, new DateTimeZone('Europe/Prague')))
            ->setTimezone(new DateTimeZone('UTC'));
    }
}
