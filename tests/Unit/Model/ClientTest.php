<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Model;

use Freelo\Sdk\Model\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testFromArrayWithFullData(): void
    {
        $data = [
            'id' => 123,
            'name' => 'Acme Corporation',
            'email' => 'contact@acme.com',
            'company' => 'Acme Corp',
            'company_id' => '12345678',
            'company_tax_id' => 'CZ12345678',
            'street' => '123 Main Street',
            'town' => 'Prague',
            'zip' => '11000',
        ];

        $client = Client::fromArray($data);

        $this->assertSame(123, $client->id);
        $this->assertSame('Acme Corporation', $client->name);
        $this->assertSame('contact@acme.com', $client->email);
        $this->assertSame('Acme Corp', $client->company);
        $this->assertSame('12345678', $client->companyId);
        $this->assertSame('CZ12345678', $client->companyTaxId);
        $this->assertSame('123 Main Street', $client->street);
        $this->assertSame('Prague', $client->town);
        $this->assertSame('11000', $client->zip);
        $this->assertSame($data, $client->data);
    }

    public function testFromArrayWithMinimalData(): void
    {
        $data = [
            'id' => 456,
            'name' => 'Simple Client',
        ];

        $client = Client::fromArray($data);

        $this->assertSame(456, $client->id);
        $this->assertSame('Simple Client', $client->name);
        $this->assertNull($client->email);
        $this->assertNull($client->company);
        $this->assertNull($client->companyId);
        $this->assertNull($client->companyTaxId);
        $this->assertNull($client->street);
        $this->assertNull($client->town);
        $this->assertNull($client->zip);
    }

    public function testFromArrayWithEmptyData(): void
    {
        $data = [];

        $client = Client::fromArray($data);

        $this->assertSame(0, $client->id);
        $this->assertSame('', $client->name);
    }

    public function testToArray(): void
    {
        $data = [
            'id' => 789,
            'name' => 'Test Client',
            'town' => 'Brno',
        ];

        $client = Client::fromArray($data);
        $result = $client->toArray();

        $this->assertSame($data, $result);
    }

    public function testConstructorWithAllParameters(): void
    {
        $client = new Client(
            id: 999,
            name: 'Direct Construction Ltd',
            email: 'test@example.com',
            company: 'Test Company',
            companyId: '87654321',
            companyTaxId: 'CZ87654321',
            street: '456 Test Avenue',
            town: 'Ostrava',
            zip: '70000',
            data: ['custom' => 'data'],
        );

        $this->assertSame(999, $client->id);
        $this->assertSame('Direct Construction Ltd', $client->name);
        $this->assertSame('87654321', $client->companyId);
        $this->assertSame('Ostrava', $client->town);
    }
}
