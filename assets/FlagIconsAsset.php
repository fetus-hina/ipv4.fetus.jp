<?php

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

final class FlagIconsAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@npm/flag-icons';

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
