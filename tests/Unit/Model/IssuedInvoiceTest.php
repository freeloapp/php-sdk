<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Model;

use Freelo\Sdk\Model\Currency;
use Freelo\Sdk\Model\IssuedInvoice;
use PHPUnit\Framework\TestCase;

class IssuedInvoiceTest extends TestCase
{
    public function testFromArrayWithFullData(): void
    {
        $data = [
            'id' => 123,
            'date_add' => '2024-01-01T10:00:00Z',
            'note' => 'Test invoice note',
            'currency' => 'CZK',
            'minutes' => 480,
            'price' => [
                'amount' => '100000',
                'currency' => 'CZK',
            ],
            'subject' => [
                'company_name' => 'Acme Corp',
                'invoice_url' => 'https://example.com/invoice/123',
            ],
            'inv_items' => [
                ['id' => 1, 'name' => 'Development', 'minutes' => 240],
                ['id' => 2, 'name' => 'Testing', 'minutes' => 240],
            ],
        ];

        $invoice = IssuedInvoice::fromArray($data);

        $this->assertSame(123, $invoice->id);
        $this->assertSame('2024-01-01T10:00:00Z', $invoice->dateAdd);
        $this->assertSame('Test invoice note', $invoice->note);
        $this->assertSame('CZK', $invoice->currency);
        $this->assertSame(480, $invoice->minutes);
        $this->assertInstanceOf(Currency::class, $invoice->price);
        $this->assertSame('100000', $invoice->price->amount);
        $this->assertSame('Acme Corp', $invoice->getCompanyName());
        $this->assertSame('https://example.com/invoice/123', $invoice->getInvoiceUrl());
        $this->assertCount(2, $invoice->invItems);
    }

    public function testFromArrayWithMinimalData(): void
    {
        $data = [
            'id' => 456,
        ];

        $invoice = IssuedInvoice::fromArray($data);

        $this->assertSame(456, $invoice->id);
        $this->assertNull($invoice->dateAdd);
        $this->assertNull($invoice->note);
        $this->assertNull($invoice->price);
        $this->assertEmpty($invoice->invItems);
    }

    public function testFromArrayWithEmptyData(): void
    {
        $data = [];

        $invoice = IssuedInvoice::fromArray($data);

        $this->assertSame(0, $invoice->id);
        $this->assertNull($invoice->dateAdd);
    }

    public function testGetHours(): void
    {
        $invoice = IssuedInvoice::fromArray(['id' => 1, 'minutes' => 120]);

        $this->assertSame(2.0, $invoice->getHours());
    }

    public function testGetHoursWithNullMinutes(): void
    {
        $invoice = IssuedInvoice::fromArray(['id' => 1]);

        $this->assertSame(0.0, $invoice->getHours());
    }

    public function testGetCompanyNameWithNoSubject(): void
    {
        $invoice = IssuedInvoice::fromArray(['id' => 1]);

        $this->assertNull($invoice->getCompanyName());
    }

    public function testGetInvoiceUrlWithNoSubject(): void
    {
        $invoice = IssuedInvoice::fromArray(['id' => 1]);

        $this->assertNull($invoice->getInvoiceUrl());
    }

    public function testToArray(): void
    {
        $data = [
            'id' => 789,
            'currency' => 'EUR',
            'minutes' => 60,
        ];

        $invoice = IssuedInvoice::fromArray($data);
        $result = $invoice->toArray();

        $this->assertSame($data, $result);
    }

    public function testConstructorWithAllParameters(): void
    {
        $price = Currency::fromArray(['amount' => '200000', 'currency' => 'EUR']);

        $invoice = new IssuedInvoice(
            id: 999,
            dateAdd: '2024-01-01T00:00:00Z',
            note: 'Test note',
            currency: 'EUR',
            minutes: 300,
            price: $price,
            subject: ['company_name' => 'Test Company', 'invoice_url' => 'https://example.com'],
            invItems: [['id' => 1, 'name' => 'Item 1']],
            data: ['custom' => 'data'],
        );

        $this->assertSame(999, $invoice->id);
        $this->assertSame('2024-01-01T00:00:00Z', $invoice->dateAdd);
        $this->assertSame('Test Company', $invoice->getCompanyName());
        $this->assertSame(5.0, $invoice->getHours());
    }
}
