<?php

/**
 * @copyright Copyright (C) 2021-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\helpers;

use Yii;

use function is_array;
use function is_string;

/**
 * Accessor for the AdSense configuration (config/params/adsense.php).
 *
 * The configuration file exists only in some environments, so it is not exposed via
 * Yii::$app->params, whose type is inferred by PHPStan from the config file contents.
 */
final class AdSenseConfig
{
    private const CONFIG_PATH = '@app/config/params/adsense.php';

    /**
     * @var array{client: string, slots: array<string, string>}|false|null
     */
    private static array|false|null $config = null;

    public static function isConfigured(): bool
    {
        return self::getClient() !== null;
    }

    public static function getClient(): ?string
    {
        return self::load()['client'] ?? null;
    }

    public static function getSlotId(string $slot): ?string
    {
        return self::load()['slots'][$slot] ?? null;
    }

    /**
     * @return array{client: string, slots: array<string, string>}|null
     */
    private static function load(): ?array
    {
        if (self::$config === null) {
            self::$config = self::parse(
                FileHelper::requireIfExists(
                    TypeHelper::shouldBeString(Yii::getAlias(self::CONFIG_PATH)),
                ),
            ) ?? false;
        }

        return self::$config === false ? null : self::$config;
    }

    /**
     * @return array{client: string, slots: array<string, string>}|null
     */
    private static function parse(mixed $config): ?array
    {
        if (!is_array($config)) {
            return null;
        }

        $client = $config['client'] ?? null;
        if (!is_string($client) || $client === '') {
            return null;
        }

        $slots = $config['slots'] ?? null;
        $validSlots = [];
        if (is_array($slots)) {
            foreach ($slots as $name => $id) {
                if (is_string($name) && is_string($id) && $id !== '') {
                    $validSlots[$name] = $id;
                }
            }
        }

        return [
            'client' => $client,
            'slots' => $validSlots,
        ];
    }
}
