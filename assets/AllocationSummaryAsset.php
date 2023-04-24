<?php

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

final class AllocationSummaryAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@app/resources';

    /**
     * @var string[]
     */
    public $js = [
        'js/chart-allocation-summary.min.js',
    ];

    /**
     * @var class-string[]
     */
    public $depends = [
        ChartColorsAsset::class,
        ChartJsAsset::class,
        JqueryAsset::class,
    ];
}
