<?php

/**
 * @copyright Copyright (C) 2021-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets\adsense;

use app\helpers\AdSenseConfig;
use yii\web\AssetBundle;

use function http_build_query;
use function vsprintf;

use const PHP_QUERY_RFC3986;

class JSLoaderAsset extends AssetBundle
{
    /**
     * @var string[]
     */
    public $js = [];

    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint
    public $jsOptions = [
        'async' => true,
        'crossorigin' => 'anonymous',
    ];

    /**
     * @return void
     */
    public function init()
    {
        parent::init();

        $client = AdSenseConfig::getClient();
        if ($client !== null) {
            $this->js[] = vsprintf('%s?%s', [
                'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js',
                http_build_query(
                    [
                        'client' => $client,
                    ],
                    '',
                    '&',
                    PHP_QUERY_RFC3986,
                ),
            ]);
        }
    }
}
