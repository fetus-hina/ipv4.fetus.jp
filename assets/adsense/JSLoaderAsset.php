<?php

declare(strict_types=1);

namespace app\assets\adsense;

use Yii;
use yii\web\AssetBundle;

use function http_build_query;
use function is_array;
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

        $params = Yii::$app->params['adsense'];
        if (is_array($params)) {
            $this->js[] = vsprintf('%s?%s', [
                'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js',
                http_build_query(
                    [
                        'client' => $params['client'],
                    ],
                    '',
                    '&',
                    PHP_QUERY_RFC3986,
                ),
            ]);
        }
    }
}
