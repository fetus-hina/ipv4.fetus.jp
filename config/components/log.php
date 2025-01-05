<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

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
