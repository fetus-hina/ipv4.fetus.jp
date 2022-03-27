<?php

declare(strict_types=1);

namespace app\assets;

use yii\bootstrap5\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\web\YiiAsset;

final class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $depends = [
        BackToTopAsset::class,
        BootstrapAsset::class,
        CardShadowAsset::class,
        DropdownToggleAsset::class,
        FocusInputAsset::class,
        JqueryAsset::class,
        LanguageSwitcherAsset::class,
        LatinFontAsset::class,
        SiteResourceAsset::class,
        YiiAsset::class,
    ];
}
