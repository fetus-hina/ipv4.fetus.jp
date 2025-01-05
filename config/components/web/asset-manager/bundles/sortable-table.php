<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\BootstrapIconsAsset;
use statink\yii2\sortableTable\JqueryStupidTableAsset;
use yii\web\JqueryAsset;

return [
    'basePath' => null,
    'baseUrl' => null,
    'sourcePath' => '@app/resources',
    'js' => [
        'js/sortable-table.min.js',
    ],
    'depends' => [
        BootstrapIconsAsset::class,
        JqueryAsset::class,
        JqueryStupidTableAsset::class,
    ],
];
