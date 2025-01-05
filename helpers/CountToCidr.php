<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

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
