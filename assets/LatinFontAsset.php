<?php

declare(strict_types=1);

namespace app\assets;

use yii\bootstrap5\BootstrapAsset;
use yii\web\AssetBundle;

final class LatinFontAsset extends AssetBundle
{
    public $sourcePath = '@app/resources';
    public $css = [
        'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap',
        'css/latin-font.min.css',
    ];
    public $depends = [
        BootstrapAsset::class,
    ];
}
