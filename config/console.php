<?php

declare(strict_types=1);

use app\gii\model\Generator as GiiModelGenerator;
use yii\gii\Module as GiiModule;

return (function (): array {
    $config = [
        'id' => 'ipv4.fetus.jp',
        'name' => 'ipv4.fetus.jp',
        'language' => 'ja-JP',
        'timeZone' => 'Asia/Tokyo',
        'basePath' => dirname(__DIR__),
        'bootstrap' => [
            'log',
        ],
        'controllerNamespace' => 'app\commands',
        'aliases' => [
            '@bower' => '@app/node_modules',
            '@npm'   => '@app/node_modules',
            '@tests' => '@app/tests',
        ],
        'components' => [
            'cache' => require(__DIR__ . '/components/cache.php'),
            'db' => require(__DIR__ . '/components/db/db.php'),
            'log' => require(__DIR__ . '/components/log.php'),
        ],
        'params' => require(__DIR__ . '/params.php'),
    ];

    if (YII_ENV_DEV) {
        $config['bootstrap'][] = 'gii';
        $config['modules']['gii'] = [
            'class' => GiiModule::class,
            'generators' => [
                'model' => [
                    'class' => GiiModelGenerator::class,
                    'templates' => [
                        'default' => '@app/views/gii/model',
                    ],
                ],
            ],
        ];
    }

    return $config;
})();
