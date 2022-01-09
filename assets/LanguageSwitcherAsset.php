<?php

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\web\YiiAsset;

final class LanguageSwitcherAsset extends AssetBundle
{
    public $sourcePath = '@app/resources';
    public $js = [
        'js/language-switcher.min.js',
    ];
    public $depends = [
        JqueryAsset::class,
        YiiAsset::class,
    ];
}
