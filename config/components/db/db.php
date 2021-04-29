<?php

declare(strict_types=1);

use yii\db\Connection;

return (function (): array {
    $config = [
        'class' => Connection::class,
        'dsn' => 'pgsql:host=localhost;dbname=ipv4app2',
        'username' => 'ipv4app2',
        'password' => file_exists(__DIR__ . '/password.php')
            ? require(__DIR__ . '/password.php')
            : 'ipv4app2',
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
