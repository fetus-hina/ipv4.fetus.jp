<?php

declare(strict_types=1);

return array_merge(require(__DIR__ . '/../web/request.php'), [
    'cookieValidationKey' => 'test',
    'enableCsrfValidation' => false,
]);
