<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\log\ConsoleTarget;
use yii\log\FileTarget;

return (function (): array {
    $targets = [
        [
            'class' => FileTarget::class,
            'levels' => [
                'error',
                'warning',
            ],
        ],
    ];

    if (PHP_SAPI === 'cli') {
        $targets[] = [
            'class' => ConsoleTarget::class,
            'exportInterval' => 1,
            'levels' => [
                'info',
                'warning',
                'error',
            ],
            'categories' => [
                'app\\commands\\*',
            ],
            'logVars' => [],
        ];
    }

    return [
        'traceLevel' => defined('YII_DEBUG') && constant('YII_DEBUG') ? 3 : 0,
        'targets' => $targets,
    ];
})();
