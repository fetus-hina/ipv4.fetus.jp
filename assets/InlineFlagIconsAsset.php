<?php

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

final class InlineFlagIconsAsset extends AssetBundle
{
    public $sourcePath = '@app/resources';
    public $css = [
        'css/inline-flag-icons.min.css',
    ];
}
