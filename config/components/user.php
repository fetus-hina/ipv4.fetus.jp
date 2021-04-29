<?php

declare(strict_types=1);

use app\models\User;

return [
    'enableAutoLogin' => false,
    'enableSession' => false,
    'identityClass' => User::class,
    'loginUrl' => null,
];
