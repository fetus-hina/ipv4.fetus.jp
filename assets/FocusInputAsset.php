<?php

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

final class FocusInputAsset extends AssetBundle
{
    public $sourcePath = '@app/resources';
    public $js = [
        'js/focus-input.min.js',
    ];
    public $depends = [
        JqueryAsset::class,
    ];
}
