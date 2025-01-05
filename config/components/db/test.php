<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

return (fn (array $db): array => array_merge($db, [
    'dsn' => 'pgsql:host=localhost;dbname=ipv4test',
    'username' => 'ipv4test',
    'password' => 'ipv4test',
]))(require __DIR__ . '/db.php');
