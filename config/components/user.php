<?php

declare(strict_types=1);

use app\models\User;

return [
    'identityClass' => User::class,
    'enableAutoLogin' => false,
];
