<?php

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
