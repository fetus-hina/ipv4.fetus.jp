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
}
