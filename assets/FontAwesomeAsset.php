<?php

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

class FontAwesomeAsset extends AssetBundle
{
    /** @var string */
    public $sourcePath = '@npm/@fortawesome/fontawesome-free/js';

    /** @var string[] */
    public $js = [
        'all.min.js',
    ];

    /** @var array<string, string> */
    public $jsOptions = [
        'data-auto-replace-svg' => 'nest',
    ];
}
