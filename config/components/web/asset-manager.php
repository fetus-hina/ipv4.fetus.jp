<?php

declare(strict_types=1);

use app\assets\BootstrapIconsAsset;
use statink\yii2\sortableTable\JqueryStupidTableAsset;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\bootstrap5\BootstrapAsset;
use yii\bootstrap5\BootstrapPluginAsset;
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
        SortableTableAsset::class => [
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
        ],
        BootstrapAsset::class => [
            'sourcePath' => '@npm/@fetus-hina/fetus.css/dist',
            'css' => [
                'bootstrap-bizudpgothic.min.css',
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
    'hashCallback' => function (string $path): string {
        $gitRevision = Yii::$app->params['gitRevision'];
        $path = is_file($path) ? dirname($path) : $path;
        if (!$gitRevision) {
            return substr(hash('sha256', $path), 0, 16);
        }

        if ($gitRevision['version']) {
            return $gitRevision['version'] . '/' . substr(hash('sha256', $path), 0, 16);
        }

        return 'rev-' . $gitRevision['short'] . '/' . substr(hash('sha256', $path), 0, 16);
    },
];
