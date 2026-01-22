<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Model;

use Freelo\Sdk\Model\HourRate;
use Freelo\Sdk\Model\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testFromArrayWithFullData(): void
    {
        $data = [
            'id' => 123,
            'fullname' => 'John Doe',
            'email' => 'john@example.com',
            'hour_rate' => [
                'amount' => '50000',
                'currency' => 'CZK',
            ],
        ];

        $user = User::fromArray($data);

        $this->assertSame(123, $user->id);
        $this->assertSame('John Doe', $user->fullname);
        $this->assertSame('john@example.com', $user->email);
        $this->assertInstanceOf(HourRate::class, $user->hourRate);
        $this->assertSame('50000', $user->hourRate->amount);
    }

    public function testFromArrayWithMinimalData(): void
    {
        $data = [
            'id' => 456,
            'fullname' => 'Jane Smith',
        ];

        $user = User::fromArray($data);

        $this->assertSame(456, $user->id);
        $this->assertSame('Jane Smith', $user->fullname);
        $this->assertNull($user->email);
        $this->assertNull($user->hourRate);
    }

    public function testFromArrayWithEmptyData(): void
    {
        $data = [];

        $user = User::fromArray($data);

        $this->assertSame(0, $user->id);
        $this->assertSame('', $user->fullname);
        $this->assertNull($user->email);
    }

    public function testToArray(): void
    {
        $data = [
            'id' => 789,
            'fullname' => 'Test User',
            'email' => 'test@example.com',
        ];

        $user = User::fromArray($data);
        $result = $user->toArray();

        $this->assertSame($data, $result);
    }

    public function testConstructorWithAllParameters(): void
    {
        $hourRate = new HourRate(
            amount: '100000',
            currency: 'EUR',
        );

        $user = new User(
            id: 999,
            fullname: 'Direct Construction',
            email: 'direct@example.com',
            hourRate: $hourRate,
            data: ['custom' => 'data'],
        );

        $this->assertSame(999, $user->id);
        $this->assertSame('Direct Construction', $user->fullname);
        $this->assertSame('direct@example.com', $user->email);
        $this->assertSame($hourRate, $user->hourRate);
    }
}
