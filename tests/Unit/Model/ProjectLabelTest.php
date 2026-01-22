<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Model;

use Freelo\Sdk\Model\ProjectLabel;
use PHPUnit\Framework\TestCase;

class ProjectLabelTest extends TestCase
{
    public function testFromArrayWithFullData(): void
    {
        $data = [
            'id' => 123,
            'name' => 'High Priority',
            'color' => '#ff0000',
        ];

        $label = ProjectLabel::fromArray($data);

        $this->assertSame(123, $label->id);
        $this->assertSame('High Priority', $label->name);
        $this->assertSame('#ff0000', $label->color);
        $this->assertSame($data, $label->data);
    }

    public function testFromArrayWithMinimalData(): void
    {
        $data = [
            'id' => 456,
            'name' => 'Development',
        ];

        $label = ProjectLabel::fromArray($data);

        $this->assertSame(456, $label->id);
        $this->assertSame('Development', $label->name);
        $this->assertNull($label->color);
    }

    public function testFromArrayWithEmptyData(): void
    {
        $data = [];

        $label = ProjectLabel::fromArray($data);

        $this->assertSame(0, $label->id);
        $this->assertSame('', $label->name);
        $this->assertNull($label->color);
    }

    public function testToArray(): void
    {
        $data = [
            'id' => 789,
            'name' => 'Test Label',
            'color' => '#00ff00',
        ];

        $label = ProjectLabel::fromArray($data);
        $result = $label->toArray();

        $this->assertSame($data, $result);
    }

    public function testConstructorWithAllParameters(): void
    {
        $label = new ProjectLabel(
            id: 999,
            name: 'Direct Construction',
            color: '#0000ff',
            data: ['custom' => 'data'],
        );

        $this->assertSame(999, $label->id);
        $this->assertSame('Direct Construction', $label->name);
        $this->assertSame('#0000ff', $label->color);
    }
}
