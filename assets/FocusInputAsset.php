<?php

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

final class FocusInputAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@app/resources';

    /**
     * @var string[]
     */
    public $js = [
        'js/focus-input.min.js',
    ];

    /**
     * @var class-string[]
     */
    public $depends = [
        JqueryAsset::class,
    ];
}
