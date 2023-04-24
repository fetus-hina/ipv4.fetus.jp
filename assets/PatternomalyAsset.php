<?php

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

final class PatternomalyAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@npm/patternomaly/dist';

    /**
     * @var string[]
     */
    public $js = [
        'patternomaly.min.js',
    ];
}
