<?php

declare(strict_types=1);

namespace app\assets;

use yii\bootstrap5\BootstrapAsset;
use yii\bootstrap5\BootstrapPluginAsset;
use yii\web\AssetBundle;

final class BackToTopAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@npm/@fetus-hina/fetus.css/dist';

    /**
     * @var string[]
     */
    public $js = [
        'fetus-css.min.js',
    ];

    /**
     * @var class-string[]
     */
    public $depends = [
        BootstrapAsset::class,
        BootstrapPluginAsset::class,
    ];
}
