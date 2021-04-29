<?php

declare(strict_types=1);

return [
    'cookieValidationKey' => file_exists(__DIR__ . '/request--cookie.php')
        ? require(__DIR__ . '/request--cookie.php')
        : '',
];
