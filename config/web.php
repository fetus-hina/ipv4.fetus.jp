<?php

declare(strict_types=1);

use app\helpers\ApplicationLanguage;
use yii\debug\Module as DebugModule;

return (function (): array {
    $config = [
        'id' => 'ipv4.fetus.jp',
        'name' => 'ipv4.fetus.jp',
        'language' => 'en-US',
        'timeZone' => 'Asia/Tokyo',
        'basePath' => dirname(__DIR__),
        'bootstrap' => [
            ApplicationLanguage::class,
            'log',
        ],
        'aliases' => [
            '@bower' => '@app/node_modules',
            '@npm' => '@app/node_modules',
        ],
        'components' => [
            'assetManager' => require __DIR__ . '/components/web/asset-manager.php',
            'cache' => require __DIR__ . '/components/cache.php',
            'db' => require __DIR__ . '/components/db/db.php',
            'errorHandler' => require __DIR__ . '/components/web/error-handler.php',
            'i18n' => require __DIR__ . '/components/i18n.php',
            'log' => require __DIR__ . '/components/log.php',
            'request' => require __DIR__ . '/components/web/request.php',
            'urlManager' => require __DIR__ . '/components/web/url-manager.php',
            'user' => require __DIR__ . '/components/user.php',
        ],
        'params' => require __DIR__ . '/params.php',
    ];

    if (
        YII_ENV_DEV &&
        file_exists(__DIR__ . '/../vendor/yiisoft/yii2-debug')
    ) {
        $config['bootstrap'][] = 'debug';
        $config['modules']['debug'] = [
            'class' => DebugModule::class,
            'allowedIPs' => [
                '127.0.0.1',
                '192.168.*',
                '::1',
            ],
        ];
    }

    return $config;
})();
