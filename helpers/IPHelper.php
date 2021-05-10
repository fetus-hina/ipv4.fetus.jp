<?php

declare(strict_types=1);

namespace app\helpers;

class IPHelper
{
    public static function bitmask(int $bits): ?int
    {
        if ($bits < 1 || $bits > 32) {
            return null;
        }

        return (0xffffffff << (32 - $bits)) & 0xffffffff;
    }
}
