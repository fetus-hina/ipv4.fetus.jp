<?php

declare(strict_types=1);

use app\helpers\FileHelper;
use yii\db\Connection;

return (fn (): array => [
    'class' => Connection::class,
    'dsn' => 'pgsql:host=localhost;dbname=ipv4app2',
    'username' => 'ipv4app2',
    'password' => FileHelper::requireIfExists(
        __DIR__ . '/password.php',
        'ipv4app2', // default password
    ),
    'charset' => 'utf8',
    'enableSchemaCache' => true,
    'schemaCacheDuration' => YII_ENV_PROD ? 604800 : 600,
    'schemaCache' => 'cache',
])();
