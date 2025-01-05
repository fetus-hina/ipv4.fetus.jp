<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

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
