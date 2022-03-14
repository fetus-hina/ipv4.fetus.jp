<?php

declare(strict_types=1);

namespace app\assets;

use yii\bootstrap5\BootstrapPluginAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

final class TooltipAsset extends AssetBundle
{
    public $sourcePath = '@app/resources';
    public $js = [
        'js/tooltip.min.js',
    ];
    public $depends = [
        BootstrapPluginAsset::class,
        JqueryAsset::class,
    ];
}
