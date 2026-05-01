<?php

declare(strict_types=1);

namespace Freelo\Sdk\Internal;

use DateTimeImmutable;
use DateTimeZone;
use Freelo\Sdk\Exception\InvalidDateTimeException;

/**
 * Parses datetime strings returned by the Freelo Public API V1.
 *
 * Per the API's "Timestamp Format" section, all `format: date-time` fields are naive
 * ISO8601 strings (no timezone designator) representing Europe/Prague local time
 * (CET / CEST, observing DST). They are not RFC3339-compliant. This parser interprets
 * such values as Europe/Prague and converts them to UTC \DateTimeImmutable.
 * RFC3339-with-timezone and pure Y-m-d dates are accepted defensively.
 *
 * @see https://api.freelo.io/docs/v1/freelo-api.yaml — see "Timestamp Format" in info.description
 */
final class DateTimeParser
{
    private const SOURCE_TIMEZONE = 'Europe/Prague';

    private const NAIVE_DATETIME = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}$/';
    private const NAIVE_DATETIME_WITH_FRACTION = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d+$/';
    private const DATE_ONLY = '/^\d{4}-\d{2}-\d{2}$/';
    private const RFC3339_TZ_SUFFIX = '/(Z|[+-]\d{2}:?\d{2})$/';

    public static function parseDateTime(mixed $value): ?DateTimeImmutable
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof DateTimeImmutable) {
            return $value->setTimezone(new DateTimeZone('UTC'));
        }

        if (!is_string($value)) {
            throw new InvalidDateTimeException(
                'Expected string|DateTimeImmutable|null, got ' . get_debug_type($value)
            );
        }

        $utc = new DateTimeZone('UTC');

        if (preg_match(self::RFC3339_TZ_SUFFIX, $value) === 1) {
            $parsed = DateTimeImmutable::createFromFormat(\DateTimeInterface::RFC3339, $value)
                ?: DateTimeImmutable::createFromFormat(\DateTimeInterface::RFC3339_EXTENDED, $value);

            if ($parsed === false) {
                try {
                    $parsed = new DateTimeImmutable($value);
                } catch (\Exception $e) {
                    throw new InvalidDateTimeException("Invalid datetime string: {$value}", 0, $e);
                }
            }

            return $parsed->setTimezone($utc);
        }

        $prague = new DateTimeZone(self::SOURCE_TIMEZONE);

        if (preg_match(self::NAIVE_DATETIME, $value) === 1) {
            $parsed = DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s', $value, $prague);
            if ($parsed === false) {
                throw new InvalidDateTimeException("Invalid naive datetime: {$value}");
            }
            return $parsed->setTimezone($utc);
        }

        if (preg_match(self::NAIVE_DATETIME_WITH_FRACTION, $value) === 1) {
            $parsed = DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.u', $value, $prague);
            if ($parsed === false) {
                throw new InvalidDateTimeException("Invalid naive datetime: {$value}");
            }
            return $parsed->setTimezone($utc);
        }

        if (preg_match(self::DATE_ONLY, $value) === 1) {
            $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $value, $prague);
            if ($parsed === false) {
                throw new InvalidDateTimeException("Invalid date: {$value}");
            }
            return $parsed->setTimezone($utc);
        }

        throw new InvalidDateTimeException("Unrecognised datetime format: {$value}");
    }

    /**
     * Format a \DateTimeImmutable as Y-m-d in Europe/Prague (for outgoing query filters).
     */
    public static function formatDate(DateTimeImmutable|string $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        return $value->setTimezone(new DateTimeZone(self::SOURCE_TIMEZONE))->format('Y-m-d');
    }
}
