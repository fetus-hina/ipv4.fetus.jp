<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

final class FlagIconsAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@vendor/lipis/flag-icons';

    /**
     * @var string[]
     */
    public $css = [
        'css/flag-icons.min.css',
    ];

    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint
    public $publishOptions = [
        'only' => [
            'css/*',
            'flags/*/*',
        ],
    ];
}
