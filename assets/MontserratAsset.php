<?php

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

final class MontserratAsset extends AssetBundle
{
    public $sourcePath = '@npm/@fontsource/montserrat';
    public $css = [
        'latin.css',
    ];
}
