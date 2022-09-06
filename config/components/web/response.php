<?php

declare(strict_types=1);

use app\formatters\CompressiveHtmlResponseFormatter;

return [
    'formatters' => [
        'compressive-html' => CompressiveHtmlResponseFormatter::class,
    ],
];
