<?php

declare(strict_types=1);

return [
    'id' => 'ipv4.fetus.jp',
    'name' => 'ipv4.fetus.jp',
    'language' => 'ja-JP',
    'timeZone' => 'Asia/Tokyo',
    'basePath' => dirname(__DIR__),
    'aliases' => [
        '@bower' => '@app/node_modules',
        '@npm'   => '@app/node_modules',
    ],
    'components' => [
        'assetManager' => require(__DIR__ . '/components/console/asset-manager.php'),
        'db' => require(__DIR__ . '/components/db/test.php'),
        'request' => require(__DIR__ . '/components/console/request.php'),
        'urlManager' => require(__DIR__ . '/components/test/url-manager.php'),
        'user' => require(__DIR__ . '/components/user.php'),
    ],
    'params' => require(__DIR__ . '/params.php'),
];
