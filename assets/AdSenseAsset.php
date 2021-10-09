<?php

declare(strict_types=1);

namespace app\assets;

use app\assets\adsense\JSLoaderAsset;
use yii\web\AssetBundle;

class AdSenseAsset extends AssetBundle
{
    /** @var string[] */
    public $depends = [
        JSLoaderAsset::class,
    ];
}
