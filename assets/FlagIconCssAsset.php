<?php

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

class FlagIconCssAsset extends AssetBundle
{
    public $sourcePath = '@npm/flag-icon-css';
    public $css = [
        'css/flag-icon.min.css',
    ];
    public $publishOptions = [
        'only' => [
            'css/*',
            'flags/*/*',
        ],
    ];
}
