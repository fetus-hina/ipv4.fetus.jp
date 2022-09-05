<?php

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
