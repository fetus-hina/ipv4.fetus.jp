<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\helpers;

use yii\helpers\Url;
use yii\web\View;

final class FaviconHelper
{
    public static function registerLinkTags(View $view): void
    {
        $view->registerLinkTag([
            'href' => Url::to('@web/favicon/favicon.svg', true),
            'rel' => 'icon',
            'sizes' => 'any',
            'type' => 'image/svg+xml',
        ]);

        $sizes = [57, 60, 72, 76, 114, 120, 144, 152, 180];
        foreach ($sizes as $size) {
            $view->registerLinkTag([
                'href' => Url::to("@web/favicon/apple-touch-icon-{$size}.png", true),
                'rel' => 'apple-touch-icon',
                'sizes' => "{$size}x{$size}",
                'type' => 'image/png',
            ]);
        }
    }
}
