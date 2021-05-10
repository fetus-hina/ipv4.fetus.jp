<?php

declare(strict_types=1);

namespace app\helpers;

class CountToCidr
{
    private const MINIMUM_BITMASK = 8;

    public static function convert(string $startAddress, int $count): ?array
    {
        if ($count < 1 || $count > 0xffffffff) {
            return null;
        }

        $iplong = @ip2long($startAddress);
        if ($iplong !== false) {
            return static::splitBlock($iplong, (int)$count);
        }

        return null;
    }

    private static function splitBlock(int $startAddress, int $count): array
    {
        $result = [];
        $endAddress = $startAddress + $count - 1;
        while ($count > 0) {
            for ($bitNum = static::MINIMUM_BITMASK; $bitNum <= 32; ++$bitNum) {
                $tmpBlockMask = IPHelper::bitmask($bitNum);
                assert($tmpBlockMask !== null);
                $tmpBlockSize = (0xffffffff & ~$tmpBlockMask) + 1;
                $tmpEndAddress = $startAddress + $tmpBlockSize - 1;
                if ($tmpEndAddress <= $endAddress) {
                    if (($startAddress & $tmpBlockMask) == ($tmpEndAddress & $tmpBlockMask)) {
                        $result[] = sprintf('%s/%d', long2ip($startAddress), $bitNum);
                        $startAddress += $tmpBlockSize;
                        $count -= $tmpBlockSize;
                        break;
                    }
                }
            }
        }

        return $result;
    }
}
