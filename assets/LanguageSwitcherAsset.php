<?php

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\web\YiiAsset;

final class LanguageSwitcherAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@app/resources';

    /**
     * @var string[]
     */
    public $js = [
        'js/language-switcher.min.js',
    ];

    /**
     * @var class-string[]
     */
    public $depends = [
        JqueryAsset::class,
        YiiAsset::class,
    ];
}
