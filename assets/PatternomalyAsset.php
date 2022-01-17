<?php

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

final class PatternomalyAsset extends AssetBundle
{
    public $sourcePath = '@npm/patternomaly/dist';
    public $js = [
        'patternomaly.js',
    ];
}
