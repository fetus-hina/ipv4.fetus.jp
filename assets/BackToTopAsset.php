<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\bootstrap5\BootstrapAsset;
use yii\bootstrap5\BootstrapPluginAsset;
use yii\web\AssetBundle;

final class BackToTopAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@npm/@jp3cki/fetus.css/dist';

    /**
     * @var string[]
     */
    public $js = [
        'fetus-css.min.js',
    ];

    /**
     * @var class-string[]
     */
    public $depends = [
        BootstrapAsset::class,
        BootstrapPluginAsset::class,
    ];
}
