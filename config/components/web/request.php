<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

return [
    'cookieValidationKey' => file_exists(__DIR__ . '/request--cookie.php')
        ? require(__DIR__ . '/request--cookie.php')
        : '',
];
