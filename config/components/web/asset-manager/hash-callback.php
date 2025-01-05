<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

return function (string $path): string {
    $gitRevision = Yii::$app->params['gitRevision'];
    $path = is_file($path) ? dirname($path) : $path;
    if (!$gitRevision) {
        return substr(hash('sha256', $path), 0, 16);
    }

    if ($gitRevision['version']) {
        return $gitRevision['version'] . '/' . substr(hash('sha256', $path), 0, 16);
    }

    return 'rev-' . $gitRevision['short'] . '/' . substr(hash('sha256', $path), 0, 16);
};
