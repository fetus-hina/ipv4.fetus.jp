<?php

declare(strict_types=1);

namespace tests\unit\helpers;

use Codeception\Test\Unit;
use app\helpers\CountToCidr;

/**
 * @covers \app\helpers\CountToCidr
 */
class CountToCidrTest extends Unit
{
    public function testConvertInvalidAddress(): void
    {
        $this->assertNull(CountToCidr::convert('a', 256));
    }

    public function testConvertInvalidCount(): void
    {
        $this->assertNull(CountToCidr::convert('127.0.0.1', 0));
        $this->assertNull(CountToCidr::convert('127.0.0.1', 0x100000000));
    }

    /**
     * @dataProvider getConvertTestData
     * @param array<int, string> $cidrs
     */
    public function testConvert(string $startIP, int $count, array $cidrs): void
    {
        $this->assertEquals($cidrs, CountToCidr::convert($startIP, $count));
    }

    public function getConvertTestData(): array
    {
        return [
            // 単純に CIDR に変換できる
            ['192.0.2.0', 256, ['192.0.2.0/24']],
            ['192.0.2.0', 128, ['192.0.2.0/25']],

            // 複数の CIDR に変換される
            ['192.0.2.0', 192, ['192.0.2.0/25', '192.0.2.128/26']],
            ['10.0.0.128', 256, ['10.0.0.128/25', '10.0.1.0/25']],

            // /8 が最大サイズになるように制御しているので /7 にならずに分割される
            ['10.0.0.0', 33554432, ['10.0.0.0/8', '11.0.0.0/8']],
        ];
    }
}
