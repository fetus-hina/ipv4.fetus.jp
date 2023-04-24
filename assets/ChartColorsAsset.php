<?php

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

final class ChartColorsAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@app/resources';

    /**
     * @var string[]
     */
    public $js = [
        'js/chart-colors.min.js',
    ];

    /**
     * @var class-string[]
     */
    public $depends = [
        PatternomalyAsset::class,
    ];
}
