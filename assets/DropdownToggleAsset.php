<?php

declare(strict_types=1);

namespace app\assets;

use yii\bootstrap5\BootstrapAsset;
use yii\web\AssetBundle;

final class DropdownToggleAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@app/resources';

    /**
     * @var string[]
     */
    public $css = [
        'css/dropdown-toggle.min.css',
    ];

    /**
     * @var class-string[]
     */
    public $depends = [
        BootstrapAsset::class,
        BootstrapIconsAsset::class,
    ];
}
