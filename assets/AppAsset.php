<?php

declare(strict_types=1);

namespace app\assets;

use yii\bootstrap5\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\web\YiiAsset;

class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/focus-input.min.js',
    ];
    public $depends = [
        BootstrapAsset::class,
        JqueryAsset::class,
        YiiAsset::class,
    ];
}
