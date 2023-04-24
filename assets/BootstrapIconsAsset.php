<?php

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

final class BootstrapIconsAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@vendor/twbs/bootstrap-icons/font';

    /**
     * @var string[]
     */
    public $css = [
        'bootstrap-icons.css',
    ];
}
