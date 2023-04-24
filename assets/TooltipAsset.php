<?php

declare(strict_types=1);

namespace app\assets;

use yii\bootstrap5\BootstrapPluginAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

final class TooltipAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@app/resources';

    /**
     * @var string[]
     */
    public $js = [
        'js/tooltip.min.js',
    ];

    /**
     * @var class-string[]
     */
    public $depends = [
        BootstrapPluginAsset::class,
        JqueryAsset::class,
    ];
}
