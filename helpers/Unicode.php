<?php

declare(strict_types=1);

namespace app\helpers;

use yii\base\InvalidArgumentException;

use function array_map;
use function implode;
use function mb_chr;
use function ord;
use function preg_match;
use function preg_split;
use function strtolower;

use const PREG_SPLIT_NO_EMPTY;

final class Unicode
{
    private const CODEPOINT_REGIONAL_INDICATOR_A = 0x1F1E6;
    private const CODEPOINT_LATIN_SMALL_A = 0x61;

    public static function asciiToRegionalIndicator(string $cc): string
    {
        if (!preg_match('/\A[a-wyz][a-z]\z/ui', $cc)) {
            throw new InvalidArgumentException("{$cc} is not a valid country code");
        }

        $cc = strtolower($cc);
        return implode('', array_map(
            fn (string $c): string => TypeHelper::shouldBeString(mb_chr(
                self::CODEPOINT_REGIONAL_INDICATOR_A + ord($c) - self::CODEPOINT_LATIN_SMALL_A,
                'UTF-8',
            )),
            TypeHelper::shouldBeArray(
                preg_split('//', $cc, -1, PREG_SPLIT_NO_EMPTY),
                TypeHelper::ARRAY_DONTCARE,
            ),
        ));
    }
}
