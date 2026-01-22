<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Model;

use Freelo\Sdk\Model\Currency;
use Freelo\Sdk\Model\TimeEstimate;
use PHPUnit\Framework\TestCase;

class TimeEstimateTest extends TestCase
{
    public function testFromArrayWithFullData(): void
    {
        $data = [
            'minutes' => 150,
            'cost' => [
                'amount' => '50000',
                'currency' => 'CZK',
            ],
        ];

        $estimate = TimeEstimate::fromArray($data);

        $this->assertSame(150, $estimate->minutes);
        $this->assertInstanceOf(Currency::class, $estimate->cost);
        $this->assertSame('50000', $estimate->cost->amount);
        $this->assertSame($data, $estimate->data);
    }

    public function testFromArrayWithMinimalData(): void
    {
        $data = [
            'minutes' => 60,
        ];

        $estimate = TimeEstimate::fromArray($data);

        $this->assertSame(60, $estimate->minutes);
        $this->assertNull($estimate->cost);
    }

    public function testFromArrayWithEmptyData(): void
    {
        $data = [];

        $estimate = TimeEstimate::fromArray($data);

        $this->assertSame(0, $estimate->minutes);
        $this->assertNull($estimate->cost);
    }

    public function testGetHours(): void
    {
        $estimate = TimeEstimate::fromArray(['minutes' => 120]);

        $this->assertSame(2.0, $estimate->getHours());
    }

    public function testGetHoursWithFractionalResult(): void
    {
        $estimate = TimeEstimate::fromArray(['minutes' => 90]);

        $this->assertSame(1.5, $estimate->getHours());
    }

    public function testGetHoursWithZero(): void
    {
        $estimate = TimeEstimate::fromArray(['minutes' => 0]);

        $this->assertSame(0.0, $estimate->getHours());
    }

    public function testGetFormattedWithHoursAndMinutes(): void
    {
        $estimate = TimeEstimate::fromArray(['minutes' => 150]);

        $this->assertSame('2h 30m', $estimate->getFormatted());
    }

    public function testGetFormattedWithOnlyHours(): void
    {
        $estimate = TimeEstimate::fromArray(['minutes' => 120]);

        $this->assertSame('2h', $estimate->getFormatted());
    }

    public function testGetFormattedWithOnlyMinutes(): void
    {
        $estimate = TimeEstimate::fromArray(['minutes' => 45]);

        $this->assertSame('45m', $estimate->getFormatted());
    }

    public function testGetFormattedWithZero(): void
    {
        $estimate = TimeEstimate::fromArray(['minutes' => 0]);

        $this->assertSame('0m', $estimate->getFormatted());
    }

    public function testToArray(): void
    {
        $data = [
            'minutes' => 180,
        ];

        $estimate = TimeEstimate::fromArray($data);
        $result = $estimate->toArray();

        $this->assertSame($data, $result);
    }

    public function testConstructorWithAllParameters(): void
    {
        $cost = Currency::fromArray(['amount' => '100000', 'currency' => 'EUR']);

        $estimate = new TimeEstimate(
            minutes: 240,
            cost: $cost,
            data: ['custom' => 'data'],
        );

        $this->assertSame(240, $estimate->minutes);
        $this->assertSame(4.0, $estimate->getHours());
        $this->assertSame('4h', $estimate->getFormatted());
        $this->assertSame($cost, $estimate->cost);
    }
}
