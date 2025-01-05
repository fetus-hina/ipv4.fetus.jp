<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\helpers;

use yii\base\Exception;
use yii\helpers\FileHelper as BaseFileHelper;

use function file_exists;
use function is_file;
use function is_readable;

final class FileHelper extends BaseFileHelper
{
    public static function requireIfExists(string $path, mixed $defaultValue = null, bool $once = false): mixed
    {
        if (@file_exists($path)) {
            if (!@is_file($path) || !@is_readable($path)) {
                throw new Exception("The file \"$path\" exists, but it is not readable");
            }

            return $once ? require_once $path : require $path;
        }

        return $defaultValue;
    }
}
