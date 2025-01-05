<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\bootstrap5\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\web\YiiAsset;

final class AppAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $basePath = '@webroot';

    /**
     * @var string
     */
    public $baseUrl = '@web';

    /**
     * @var class-string[]
     */
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
