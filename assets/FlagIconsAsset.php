<?php

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

final class FlagIconsAsset extends AssetBundle
{
    public $sourcePath = '@npm/flag-icons';
    public $css = [
        'css/flag-icons.min.css',
    ];
    public $publishOptions = [
        'only' => [
            'css/*',
            'flags/*/*',
        ],
    ];
}
