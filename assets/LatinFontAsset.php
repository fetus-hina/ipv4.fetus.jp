<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\bootstrap5\BootstrapAsset;
use yii\web\AssetBundle;

final class LatinFontAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@app/resources';

    /**
     * @var string[]
     */
    public $css = [
        'css/latin-font.min.css',
    ];

    /**
     * @var class-string[]
     */
    public $depends = [
        BootstrapAsset::class,
        MontserratAsset::class,
    ];
}
