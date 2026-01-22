<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Model;

use Freelo\Sdk\Model\Currency;
use PHPUnit\Framework\TestCase;

class CurrencyTest extends TestCase
{
    public function testFromArrayWithFullData(): void
    {
        $data = [
            'amount' => '100000',
            'currency' => 'CZK',
        ];

        $currency = Currency::fromArray($data);

        $this->assertSame('100000', $currency->amount);
        $this->assertSame('CZK', $currency->currency);
        $this->assertSame($data, $currency->data);
    }

    public function testFromArrayWithEmptyData(): void
    {
        $data = [];

        $currency = Currency::fromArray($data);

        $this->assertSame('0', $currency->amount);
        $this->assertSame('CZK', $currency->currency);
    }

    public function testGetDecimalAmount(): void
    {
        $currency = Currency::fromArray(['amount' => '100000', 'currency' => 'CZK']);

        $this->assertSame(1000.0, $currency->getDecimalAmount());
    }

    public function testGetDecimalAmountWithZero(): void
    {
        $currency = Currency::fromArray(['amount' => '0', 'currency' => 'CZK']);

        $this->assertSame(0.0, $currency->getDecimalAmount());
    }

    public function testGetDecimalAmountWithSmallValue(): void
    {
        $currency = Currency::fromArray(['amount' => '50', 'currency' => 'CZK']);

        $this->assertSame(0.5, $currency->getDecimalAmount());
    }

    public function testGetFormatted(): void
    {
        $currency = Currency::fromArray(['amount' => '100000', 'currency' => 'CZK']);

        $this->assertSame('1,000.00 CZK', $currency->getFormatted());
    }

    public function testGetFormattedWithDifferentCurrency(): void
    {
        $currency = Currency::fromArray(['amount' => '25050', 'currency' => 'EUR']);

        $this->assertSame('250.50 EUR', $currency->getFormatted());
    }

    public function testToArray(): void
    {
        $data = [
            'amount' => '50000',
            'currency' => 'USD',
        ];

        $currency = Currency::fromArray($data);
        $result = $currency->toArray();

        $this->assertSame($data, $result);
    }

    public function testConstructorWithAllParameters(): void
    {
        $currency = new Currency(
            amount: '200000',
            currency: 'EUR',
            data: ['custom' => 'data'],
        );

        $this->assertSame('200000', $currency->amount);
        $this->assertSame('EUR', $currency->currency);
        $this->assertSame(2000.0, $currency->getDecimalAmount());
    }
}
