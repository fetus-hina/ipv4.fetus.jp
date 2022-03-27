<?php

declare(strict_types=1);

namespace app\assets;

use yii\bootstrap5\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

final class SiteResourceAsset extends AssetBundle
{
    public $sourcePath = '@app/resources';
    public $css = [
        'css/site.min.css',
    ];
    public $depends = [
        BootstrapAsset::class,
        YiiAsset::class,
    ];
}
