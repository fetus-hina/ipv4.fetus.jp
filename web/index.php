<?php

declare(strict_types=1);

use yii\web\Application;

if (!file_exists(__DIR__ . '/../.production')) {
    defined('YII_DEBUG') || define('YII_DEBUG', true);
    defined('YII_ENV') || define('YII_ENV', 'dev');
}

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/../config/bootstrap.php';

(new Application(require __DIR__ . '/../config/web.php'))
    ->run();
