<?php

declare(strict_types=1);

namespace app\assets;

use yii\bootstrap5\BootstrapAsset;
use yii\web\AssetBundle;

final class DropdownToggleAsset extends AssetBundle
{
    public $sourcePath = '@app/resources';
    public $css = [
        'css/dropdown-toggle.min.css',
    ];
    public $depends = [
        BootstrapAsset::class,
        BootstrapIconsAsset::class,
    ];
}
