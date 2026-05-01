<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Internal;

use DateTimeImmutable;
use DateTimeZone;
use Freelo\Sdk\Exception\InvalidDateTimeException;
use Freelo\Sdk\Internal\DateTimeParser;
use PHPUnit\Framework\TestCase;

class DateTimeParserTest extends TestCase
{
    public function testNullReturnsNull(): void
    {
        $this->assertNull(DateTimeParser::parseDateTime(null));
    }

    public function testEmptyStringReturnsNull(): void
    {
        $this->assertNull(DateTimeParser::parseDateTime(''));
    }

    public function testNaiveDatetimeInWinterUsesCet(): void
    {
        $result = DateTimeParser::parseDateTime('2024-01-15T10:00:00');

        $this->assertNotNull($result);
        $this->assertSame('UTC', $result->getTimezone()->getName());
        // Prague in January = CET (UTC+1) → 10:00 Prague = 09:00 UTC
        $this->assertSame('2024-01-15T09:00:00+00:00', $result->format('c'));
    }

    public function testNaiveDatetimeInSummerUsesCest(): void
    {
        $result = DateTimeParser::parseDateTime('2024-07-15T10:00:00');

        $this->assertNotNull($result);
        // Prague in July = CEST (UTC+2) → 10:00 Prague = 08:00 UTC
        $this->assertSame('2024-07-15T08:00:00+00:00', $result->format('c'));
    }

    public function testRfc3339WithZSuffixIsConvertedToUtc(): void
    {
        $result = DateTimeParser::parseDateTime('2024-01-15T10:00:00Z');

        $this->assertNotNull($result);
        $this->assertSame('UTC', $result->getTimezone()->getName());
        $this->assertSame('2024-01-15T10:00:00+00:00', $result->format('c'));
    }

    public function testRfc3339WithOffsetIsConvertedToUtc(): void
    {
        $result = DateTimeParser::parseDateTime('2024-01-15T10:00:00+05:00');

        $this->assertNotNull($result);
        $this->assertSame('UTC', $result->getTimezone()->getName());
        $this->assertSame('2024-01-15T05:00:00+00:00', $result->format('c'));
    }

    public function testDateOnlyTreatedAsPragueMidnight(): void
    {
        $result = DateTimeParser::parseDateTime('2024-01-15');

        $this->assertNotNull($result);
        // Prague midnight in January (CET) = 23:00 UTC previous day
        $this->assertSame('2024-01-14T23:00:00+00:00', $result->format('c'));
    }

    public function testDateOnlyInSummer(): void
    {
        $result = DateTimeParser::parseDateTime('2024-07-15');

        $this->assertNotNull($result);
        // Prague midnight in July (CEST) = 22:00 UTC previous day
        $this->assertSame('2024-07-14T22:00:00+00:00', $result->format('c'));
    }

    public function testDstFallBackOctoberAmbiguous(): void
    {
        // 2024-10-27 02:30 in Prague occurs twice (DST end: 03:00 CEST → 02:00 CET).
        // PHP picks the second occurrence (CET, UTC+1) → 01:30 UTC.
        $result = DateTimeParser::parseDateTime('2024-10-27T02:30:00');

        $this->assertNotNull($result);
        $this->assertSame('2024-10-27T01:30:00+00:00', $result->format('c'));
    }

    public function testDstSpringForwardSkippedHourIsAdjusted(): void
    {
        // 2024-03-31 02:30 in Prague does not exist (clock jumps from 02:00 → 03:00).
        // PHP shifts it forward; we just verify it parses without error and is in UTC.
        $result = DateTimeParser::parseDateTime('2024-03-31T02:30:00');

        $this->assertNotNull($result);
        $this->assertSame('UTC', $result->getTimezone()->getName());
    }

    public function testNaiveDatetimeWithFractionalSeconds(): void
    {
        $result = DateTimeParser::parseDateTime('2024-01-15T10:00:00.123');

        $this->assertNotNull($result);
        $this->assertSame('UTC', $result->getTimezone()->getName());
        $this->assertSame('2024-01-15T09:00:00+00:00', $result->format('c'));
    }

    public function testDateTimeImmutablePassthroughIsConvertedToUtc(): void
    {
        $input = new DateTimeImmutable('2024-01-15T10:00:00', new DateTimeZone('Europe/Prague'));
        $result = DateTimeParser::parseDateTime($input);

        $this->assertNotNull($result);
        $this->assertSame('UTC', $result->getTimezone()->getName());
        $this->assertSame('2024-01-15T09:00:00+00:00', $result->format('c'));
    }

    public function testGarbageStringThrows(): void
    {
        $this->expectException(InvalidDateTimeException::class);
        DateTimeParser::parseDateTime('not a date');
    }

    public function testNonStringNonDateTimeThrows(): void
    {
        $this->expectException(InvalidDateTimeException::class);
        /** @phpstan-ignore-next-line intentional bad input */
        DateTimeParser::parseDateTime(123);
    }

    public function testFormatDateFromDateTimeImmutableUsesPragueTimezone(): void
    {
        // 2024-04-30T23:00 UTC = 2024-05-01 01:00 CEST → date is 2024-05-01
        $dt = new DateTimeImmutable('2024-04-30T23:00:00', new DateTimeZone('UTC'));

        $this->assertSame('2024-05-01', DateTimeParser::formatDate($dt));
    }

    public function testFormatDatePassesStringThrough(): void
    {
        $this->assertSame('2024-04-15', DateTimeParser::formatDate('2024-04-15'));
    }
}
