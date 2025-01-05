<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\helpers;

use DeviceDetector\ClientHints as MatomoCH;
use Yii;
use yii\helpers\Json;

use function array_filter;
use function file_exists;
use function hash_file;
use function hash_hmac;
use function is_array;
use function is_bool;
use function is_file;
use function is_readable;
use function is_string;
use function method_exists;
use function preg_match;
use function str_replace;
use function strtolower;
use function trim;

use const ARRAY_FILTER_USE_BOTH;
use const ARRAY_FILTER_USE_KEY;

final class ClientHints
{
    public static function factory(): MatomoCH
    {
        $profiler = new Profiler(__METHOD__, __METHOD__);
        try {
            return MatomoCH::factory(self::getCHHeaders());
        } finally {
            unset($profiler);
        }
    }

    /**
     * @param array<string, string>|null $headers
     * @return array<string, string>
     */
    public static function getCHHeaders(?array $headers = null): array
    {
        // phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
        return self::filterCHHeaders(is_array($headers) ? $headers : $_SERVER);
    }

    /**
     * @param array<string, string> $headers
     * @return array<string, string>
     */
    private static function filterCHHeaders(array $headers): array
    {
        return array_filter(
            $headers,
            function (string $k): bool {
                $k = str_replace('_', '-', strtolower($k));
                return (bool)preg_match('/^http-sec-ua\b/', $k) ||
                    $k === 'http-x-requested-with';
            },
            ARRAY_FILTER_USE_KEY,
        );
    }

    public static function getCacheId(MatomoCH $ch): string
    {
        $profiler = new Profiler(__METHOD__, __METHOD__);
        try {
            $getters = [
                'app' => 'getApp',
                'architecture' => 'getArchitecture',
                'bitness' => 'getBitness',
                'brandList' => 'getBrandList',
                'brandVersion' => 'getBrandVersion',
                'mobile' => 'isMobile',
                'model' => 'getModel',
                'operatingSystem' => 'getOperatingSystem',
                'operatingSystemVersion' => 'getOperatingSystemVersion',
            ];

            /** @var array<string, bool|non-empty-string|array<non-empty-string, non-empty-string>> $data */
            $data = [];
            foreach ($getters as $k => $getterName) {
                if (!method_exists($ch, $getterName)) {
                    continue;
                }

                $v = $ch->$getterName();
                if (is_bool($v)) {
                    $data[$k] = $v;
                } elseif (is_string($v)) {
                    $v = trim($v);
                    if ($v !== '') {
                        $data[$k] = $v;
                    }
                } elseif (is_array($v)) {
                    $v = self::prepareList($v);
                    if ($v) {
                        $data[$k] = $v;
                    }
                }
            }

            return hash_hmac('sha256', Json::encode($data), self::getLibraryVersion());
        } finally {
            unset($profiler);
        }
    }

    /**
     * @return array<non-empty-string, non-empty-string>
     */
    private static function prepareList(array $data): array
    {
        return array_filter(
            $data,
            fn ($v, $k): bool => is_string($k) && is_string($v) && $k !== '' && $v !== '',
            ARRAY_FILTER_USE_BOTH,
        );
    }

    private static function getLibraryVersion(): string
    {
        $profiler = new Profiler(__METHOD__, __METHOD__);
        try {
            // FIXME: matomo/device-detector のみのバージョンを取得するようにする
            $lockFile = Yii::getAlias('@app/composer.lock');
            if (
                !is_string($lockFile) ||
                !file_exists($lockFile) ||
                !is_readable($lockFile) ||
                !is_file($lockFile)
            ) {
                return 'UNKNOWN';
            }

            return hash_file('sha256', $lockFile) ?: 'UNKNOWN';
        } finally {
            unset($profiler);
        }
    }
}
