<?php

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

final class MontserratAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@app/resources/fonts/montserrat/dist/webfont';

    /**
     * @var string[]
     */
    public $css = [
        'montserrat.min.css',
    ];
}
