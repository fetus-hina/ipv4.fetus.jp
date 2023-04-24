<?php

declare(strict_types=1);

namespace app\assets;

use yii\bootstrap5\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

final class SiteResourceAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@app/resources';

    /**
     * @var string[]
     */
    public $css = [
        'css/site.min.css',
    ];

    /**
     * @var class-string[]
     */
    public $depends = [
        BootstrapAsset::class,
        YiiAsset::class,
    ];
}
