<?php

declare(strict_types=1);

namespace app\assets;

use app\assets\adsense\JSLoaderAsset;
use yii\web\AssetBundle;
use yii\web\View;

class AdSenseAsset extends AssetBundle
{
    /** @var string[] */
    public $depends = [
        JSLoaderAsset::class,
    ];

    public static function register($view)
    {
        if ($view instanceof View) {
            $view->registerJs(
                '(adsbygoogle=window.adsbygoogle||[]).push({});',
                View::POS_HEAD,
                __METHOD__
            );
        }
        return parent::register($view);
    }
}
