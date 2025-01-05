<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use statink\yii2\sortableTable\JqueryStupidTableAsset;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\bootstrap5\BootstrapAsset;
use yii\bootstrap5\BootstrapPluginAsset;
use yii\grid\GridViewAsset;
use yii\validators\PunycodeAsset;
use yii\validators\ValidationAsset;
use yii\web\JqueryAsset;
use yii\web\YiiAsset;
use yii\widgets\ActiveFormAsset;
use yii\widgets\MaskedInputAsset;
use yii\widgets\PjaxAsset;

return [
    ActiveFormAsset::class => require __DIR__ . '/bundles/yii-active-form.php',
    BootstrapAsset::class => require __DIR__ . '/bundles/bootstrap.php',
    BootstrapPluginAsset::class => require __DIR__ . '/bundles/bootstrap-plugin.php',
    GridViewAsset::class => require __DIR__ . '/bundles/yii-gridview.php',
    JqueryAsset::class => require __DIR__ . '/bundles/jquery.php',
    JqueryStupidTableAsset::class => require __DIR__ . '/bundles/jquery-stupid-table.php',
    MaskedInputAsset::class => require __DIR__ . '/bundles/masked-input.php',
    PjaxAsset::class => require __DIR__ . '/bundles/pjax.php',
    PunycodeAsset::class => require __DIR__ . '/bundles/punycode.php',
    SortableTableAsset::class => require __DIR__ . '/bundles/sortable-table.php',
    ValidationAsset::class => require __DIR__ . '/bundles/yii-validation.php',
    YiiAsset::class => require __DIR__ . '/bundles/yii.php',
];
