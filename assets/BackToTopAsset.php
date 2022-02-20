<?php

declare(strict_types=1);

namespace app\assets;

use yii\bootstrap5\BootstrapAsset;
use yii\bootstrap5\BootstrapPluginAsset;
use yii\web\AssetBundle;

final class BackToTopAsset extends AssetBundle
{
    public $sourcePath = '@npm/@fetus-hina/fetus.css/dist';
    public $js = [
        'fetus-css.min.js',
    ];
    public $depends = [
        BootstrapAsset::class,
        BootstrapPluginAsset::class,
    ];
}
