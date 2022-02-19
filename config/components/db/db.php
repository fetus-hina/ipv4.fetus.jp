<?php

declare(strict_types=1);

use app\helpers\FileHelper;
use yii\db\Connection;

return (function (): array {
    $config = [
        'class' => Connection::class,
        'dsn' => 'pgsql:host=localhost;dbname=ipv4app2',
        'username' => 'ipv4app2',
        'password' => FileHelper::requireIfExists(
            __DIR__ . '/password.php',
            'ipv4app2', // default password
        ),
        'charset' => 'utf8',
    ];

    if (YII_ENV_PROD) {
        $config = array_merge($config, [
            'enableSchemaCache' => true,
            'schemaCacheDuration' => 60,
            'schemaCache' => 'cache',
        ]);
    }

    return $config;
})();
