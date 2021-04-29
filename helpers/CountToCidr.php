<?php

declare(strict_types=1);

namespace app\helpers;

class CountToCidr
{
    private const MINIMUM_BITMASK = 8;

    public static function convert(string $start_addr, int $count): ?array
    {
        $iplong = @ip2long($start_addr);
        if ($iplong !== false) {
            return static::splitBlock($iplong, (int)$count);
        }

        return null;
    }

    private static function splitBlock(int $start_ip, int $count): array
    {
        $result = [];
        $end_ip = $start_ip + $count - 1;
        while ($count > 0) {
            for ($bitnum = static::MINIMUM_BITMASK; $bitnum <= 32; ++$bitnum) {
                $tmpblockmask = static::bitmask($bitnum);
                $tmpblocksize = (0xffffffff & ~$tmpblockmask) + 1;
                $tmpendaddress = $start_ip + $tmpblocksize - 1;
                if ($tmpendaddress <= $end_ip) {
                    if (($start_ip & $tmpblockmask) == ($tmpendaddress & $tmpblockmask)) {
                        $result[] = sprintf('%s/%d', long2ip($start_ip), $bitnum);
                        $start_ip += $tmpblocksize;
                        $count -= $tmpblocksize;
                        break;
                    }
                }
            }
        }

        return $result;
    }

    private static function bitmask(int $bits): int
    {
        assert(1 <= $bits && $bits <= 32);
        return (0xffffffff << (32 - $bits)) & 0xffffffff;
    }
}
