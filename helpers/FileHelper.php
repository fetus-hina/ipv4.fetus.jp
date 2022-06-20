<?php

declare(strict_types=1);

namespace app\helpers;

use yii\base\Exception;
use yii\helpers\FileHelper as BaseFileHelper;

final class FileHelper extends BaseFileHelper
{
    public static function requireIfExists(string $path, mixed $defaultValue = null, bool $once = false): mixed
    {
        if (@\file_exists($path)) {
            if (!@\is_file($path) || !@\is_readable($path)) {
                throw new Exception("The file \"$path\" exists, but it is not readable");
            }

            return $once ? require_once $path : require $path;
        }

        return $defaultValue;
    }
}
