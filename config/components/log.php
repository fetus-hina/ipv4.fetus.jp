<?php

declare(strict_types=1);

use yii\log\FileTarget;

return [
    'traceLevel' => defined('YII_DEBUG') && constant('YII_DEBUG') ? 3 : 0,
    'targets' => [
        [
            'class' => FileTarget::class,
            'levels' => [
                'error',
                'warning',
            ],
        ],
    ],
];
