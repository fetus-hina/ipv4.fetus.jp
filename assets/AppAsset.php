<?php

declare(strict_types=1);

namespace app\assets;

use yii\bootstrap4\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

class AppAsset extends AssetBundle
{
    /** @var string[] */
    public $depends = [
        BootstrapAsset::class,
        FontAwesomeAsset::class,
        YiiAsset::class,
    ];
}
