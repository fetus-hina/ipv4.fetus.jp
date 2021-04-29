<?php

declare(strict_types=1);

use yii\db\Connection;

return [
    'class' => Connection::class,
    'dsn' => 'pgsql:host=localhost;dbname=ipv4',
    'username' => 'ipv4',
    'password' => file_exists(__DIR__ . '/password.php')
        ? require(__DIR__ . '/password.php')
        : 'ipv4',
    'charset' => 'utf8',
    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
