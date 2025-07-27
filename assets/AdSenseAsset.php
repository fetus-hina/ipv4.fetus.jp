<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use app\assets\adsense\JSLoaderAsset;
use yii\web\AssetBundle;

class AdSenseAsset extends AssetBundle
{
    /**
     * @var class-string[]
     */
    public $depends = [
        JSLoaderAsset::class,
    ];
}
