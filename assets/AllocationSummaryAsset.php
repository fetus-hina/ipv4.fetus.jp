<?php

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

final class AllocationSummaryAsset extends AssetBundle
{
    public $sourcePath = '@app/resources';
    public $js = [
        'js/chart-allocation-summary.min.js',
    ];
    public $depends = [
        ChartColorsAsset::class,
        ChartJsAsset::class,
        JqueryAsset::class,
    ];
}
