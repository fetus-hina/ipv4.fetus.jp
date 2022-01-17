<?php

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

final class ChartColorsAsset extends AssetBundle
{
    public $sourcePath = '@app/resources';
    public $js = [
        'js/chart-colors.min.js',
    ];
    public $depends = [
        PatternomalyAsset::class,
    ];
}
