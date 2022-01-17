<?php

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

final class ChartJsAsset extends AssetBundle
{
    public $sourcePath = '@npm/chart.js/dist';
    public $js = [
        'chart.min.js',
    ];
}
