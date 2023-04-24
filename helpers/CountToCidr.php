<?php

declare(strict_types=1);

namespace app\helpers;

use function ip2long;

class CountToCidr
{
    /**
     * @return string[]
     */
    public static function convert(string $startAddress, int $count): ?array
    {
        if ($count < 1 || $count > 0xffffffff) {
            return null;
        }

        $iplong = @ip2long($startAddress);
        if ($iplong !== false) {
            return IPHelper::splitBlock($iplong, (int)$count);
        }

        return null;
    }
}
