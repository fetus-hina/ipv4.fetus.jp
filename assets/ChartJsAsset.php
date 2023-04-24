<?php

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

final class ChartJsAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@npm/chart.js/dist';

    /**
     * @var string[]
     */
    public $js = [
        'chart.umd.js',
    ];
}
