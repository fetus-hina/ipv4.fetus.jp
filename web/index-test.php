<?php

declare(strict_types=1);

use yii\web\Application;

if (
    file_exists(__DIR__ . '/../.production') ||
    !in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)
) {
    die('You are not allowed to access this file.');
}

define('YII_DEBUG', true);
define('YII_ENV', 'test');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/../config/bootstrap.php';

(new Application(require __DIR__ . '/../config/test.php'))
    ->run();
