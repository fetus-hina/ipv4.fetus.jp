<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\User;

return [
    'enableAutoLogin' => false,
    'enableSession' => false,
    'identityClass' => User::class,
    'loginUrl' => null,
];
