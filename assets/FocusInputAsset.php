<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

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
