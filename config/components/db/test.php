<?php

declare(strict_types=1);

return (function (array $db): array {
    return array_merge($db, [
        'dsn' => 'pgsql:host=localhost;dbname=ipv4test',
        'username' => 'ipv4test',
        'password' => 'ipv4test',
    ]);
})(require(__DIR__ . '/db.php'));
