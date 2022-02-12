<?php

declare(strict_types=1);

namespace app\assets;

use yii\bootstrap5\BootstrapAsset;
use yii\web\AssetBundle;

final class LatinFontAsset extends AssetBundle
{
    public $sourcePath = '@app/resources';
    public $css = [
        'css/latin-font.min.css',
    ];
    public $depends = [
        BootstrapAsset::class,
        MontserratAsset::class,
    ];
}
