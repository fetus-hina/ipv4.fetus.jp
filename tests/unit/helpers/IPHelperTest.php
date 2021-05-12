<?php

declare(strict_types=1);

namespace tests\unit\helpers;

use Codeception\Test\Unit;
use app\helpers\IPHelper;

class IPHelperTest extends Unit
{
    public function testBitmask(): void
    {
        $this->assertNull(IPHelper::bitmask(0));
        $this->assertNull(IPHelper::bitmask(33));
        $this->assertEquals(0x80000000, IPHelper::bitmask(1));
        $this->assertEquals(0xffffffff, IPHelper::bitmask(32));
        $this->assertEquals(0xffffff00, IPHelper::bitmask(24));
        $this->assertEquals(0xff000000, IPHelper::bitmask(8));
    }

    /**
     * @dataProvider getSplitBlockTestData
     * @param array<int, string> $cidrs
     */
    public function testSplitBlock(string $startIP, int $count, array $cidrs): void
    {
        $this->assertEquals(
            $cidrs,
            IPHelper::splitBlock(
                ip2long($startIP),
                $count
            )
        );
    }

    public function getSplitBlockTestData(): array
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
