<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

return array_merge(require(__DIR__ . '/../web/request.php'), [
    'cookieValidationKey' => 'test',
    'enableCsrfValidation' => false,
]);
