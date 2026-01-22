<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Model;

use Freelo\Sdk\Model\State;
use PHPUnit\Framework\TestCase;

class StateTest extends TestCase
{
    public function testFromArrayWithFullData(): void
    {
        $data = [
            'id' => 1,
            'state' => 'active',
            'extra_field' => 'extra_value',
        ];

        $state = State::fromArray($data);

        $this->assertSame(1, $state->id);
        $this->assertSame('active', $state->state);
        $this->assertSame($data, $state->data);
    }

    public function testFromArrayWithMinimalData(): void
    {
        $data = [];

        $state = State::fromArray($data);

        $this->assertSame(0, $state->id);
        $this->assertSame('', $state->state);
    }

    public function testIsActive(): void
    {
        $activeState = State::fromArray(['id' => 1, 'state' => 'active']);
        $archivedState = State::fromArray(['id' => 2, 'state' => 'archived']);

        $this->assertTrue($activeState->isActive());
        $this->assertFalse($archivedState->isActive());
    }

    public function testIsArchived(): void
    {
        $archivedState = State::fromArray(['id' => 2, 'state' => 'archived']);
        $activeState = State::fromArray(['id' => 1, 'state' => 'active']);

        $this->assertTrue($archivedState->isArchived());
        $this->assertFalse($activeState->isArchived());
    }

    public function testIsFinished(): void
    {
        $finishedState = State::fromArray(['id' => 3, 'state' => 'finished']);
        $activeState = State::fromArray(['id' => 1, 'state' => 'active']);

        $this->assertTrue($finishedState->isFinished());
        $this->assertFalse($activeState->isFinished());
    }

    public function testToArray(): void
    {
        $data = [
            'id' => 1,
            'state' => 'active',
        ];

        $state = State::fromArray($data);
        $result = $state->toArray();

        $this->assertSame($data, $result);
    }

    public function testConstructorWithAllParameters(): void
    {
        $state = new State(
            id: 5,
            state: 'custom',
            data: ['custom' => 'data'],
        );

        $this->assertSame(5, $state->id);
        $this->assertSame('custom', $state->state);
        $this->assertSame(['custom' => 'data'], $state->data);
    }
}
