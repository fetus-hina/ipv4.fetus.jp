<?php

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

final class InlineFlagIconsAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@app/resources';

    /**
     * @var string[]
     */
    public $css = [
        'css/inline-flag-icons.min.css',
    ];
}
