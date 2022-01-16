<?php

declare(strict_types=1);

namespace app\helpers;

class IPHelper
{
    private const MINIMUM_SPLIT_BITLEN = 8;

    public static function bitmask(int $bits): ?int
    {
        if ($bits < 1 || $bits > 32) {
            return null;
        }

        // phpcs:ignore SlevomatCodingStandard.PHP.UselessParentheses.UselessParentheses
        return (0xffffffff << (32 - $bits)) & 0xffffffff;
    }

    public static function splitBlock(int $startAddress, int $count): array
    {
        $result = [];
        $endAddress = $startAddress + $count - 1;
        while ($count > 0) {
            for ($bitNum = self::MINIMUM_SPLIT_BITLEN; $bitNum <= 32; ++$bitNum) {
                $tmpBlockMask = static::bitmask($bitNum);
                assert($tmpBlockMask !== null);
                $tmpBlockSize = (0xffffffff & ~$tmpBlockMask) + 1;
                $tmpEndAddress = $startAddress + $tmpBlockSize - 1;
                if ($tmpEndAddress <= $endAddress) {
                    if (($startAddress & $tmpBlockMask) === ($tmpEndAddress & $tmpBlockMask)) {
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
