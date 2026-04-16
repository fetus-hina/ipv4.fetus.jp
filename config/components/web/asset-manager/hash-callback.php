<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

return function (string $path): string {
    $gitRevision = Yii::$app->params['gitRevision'] ?? null;
    $path = is_file($path) ? dirname($path) : $path;
    if (!is_array($gitRevision)) {
        return substr(hash('sha256', $path), 0, 16);
    }

    $version = $gitRevision['version'] ?? null;
    if (is_string($version) && $version !== '') {
        return $version . '/' . substr(hash('sha256', $path), 0, 16);
    }

    $short = $gitRevision['short'] ?? '';
    return 'rev-' . (is_string($short) ? $short : '') . '/' . substr(hash('sha256', $path), 0, 16);
};
