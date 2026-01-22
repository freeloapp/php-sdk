<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Model;

use Freelo\Sdk\Model\HourRate;
use PHPUnit\Framework\TestCase;

class HourRateTest extends TestCase
{
    public function testFromArrayWithFullData(): void
    {
        $data = [
            'amount' => '50000',
            'currency' => 'CZK',
        ];

        $hourRate = HourRate::fromArray($data);

        $this->assertSame('50000', $hourRate->amount);
        $this->assertSame('CZK', $hourRate->currency);
        $this->assertSame($data, $hourRate->data);
    }

    public function testFromArrayWithEmptyData(): void
    {
        $data = [];

        $hourRate = HourRate::fromArray($data);

        $this->assertSame('0', $hourRate->amount);
        $this->assertSame('CZK', $hourRate->currency);
    }

    public function testGetDecimalAmount(): void
    {
        $hourRate = HourRate::fromArray(['amount' => '50000', 'currency' => 'CZK']);

        $this->assertSame(500.0, $hourRate->getDecimalAmount());
    }

    public function testGetDecimalAmountWithZero(): void
    {
        $hourRate = HourRate::fromArray(['amount' => '0', 'currency' => 'CZK']);

        $this->assertSame(0.0, $hourRate->getDecimalAmount());
    }

    public function testGetFormatted(): void
    {
        $hourRate = HourRate::fromArray(['amount' => '50000', 'currency' => 'CZK']);

        $this->assertSame('500.00 CZK', $hourRate->getFormatted());
    }

    public function testGetFormattedWithDifferentCurrency(): void
    {
        $hourRate = HourRate::fromArray(['amount' => '10050', 'currency' => 'EUR']);

        $this->assertSame('100.50 EUR', $hourRate->getFormatted());
    }

    public function testToArray(): void
    {
        $data = [
            'amount' => '75000',
            'currency' => 'USD',
        ];

        $hourRate = HourRate::fromArray($data);
        $result = $hourRate->toArray();

        $this->assertSame($data, $result);
    }

    public function testConstructorWithAllParameters(): void
    {
        $hourRate = new HourRate(
            amount: '100000',
            currency: 'EUR',
            data: ['custom' => 'data'],
        );

        $this->assertSame('100000', $hourRate->amount);
        $this->assertSame('EUR', $hourRate->currency);
        $this->assertSame(1000.0, $hourRate->getDecimalAmount());
    }
}
