<?php

declare(strict_types=1);

use statink\yii2\sortableTable\JqueryStupidTableAsset;
use yii\bootstrap4\BootstrapAsset;
use yii\bootstrap4\BootstrapPluginAsset;
use yii\validators\PunycodeAsset;
use yii\web\JqueryAsset;
use yii\widgets\MaskedInputAsset;
use yii\widgets\PjaxAsset;

return [
    'linkAssets' => true,
    'appendTimestamp' => false,
    'bundles' => [
        JqueryStupidTableAsset::class => [
            'sourcePath' => '@npm/stupid-table-plugin',
        ],
        BootstrapAsset::class => [
            'sourcePath' => '@npm/@fetus-hina/fetus.css/dist',
            'css' => [
                'bootstrap.min.css',
            ],
        ],
        BootstrapPluginAsset::class => [
            'sourcePath' => '@npm/bootstrap/dist',
            'js' => [
                'js/bootstrap.bundle.min.js',
            ],
        ],
        PunycodeAsset::class => [
            'sourcePath' => '@npm/punycode',
        ],
        JqueryAsset::class => [
            'sourcePath' => '@npm/jquery/dist',
            'js' => [
                'jquery.min.js',
            ],
        ],
        MaskedInputAsset::class => [
            'sourcePath' => '@npm/inputmask/dist',
        ],
        PjaxAsset::class => [
            'sourcePath' => '@npm/yii2-pjax',
        ],
    ],
];
