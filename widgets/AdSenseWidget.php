<?php

declare(strict_types=1);

namespace app\widgets;

use Yii;
use app\assets\AdSenseAsset;
use yii\base\Widget;
use yii\helpers\Html;

use function implode;
use function is_array;
use function is_string;
use function preg_match;
use function vsprintf;

final class AdSenseWidget extends Widget
{
    public const SIZE_728_90 = '728x90';
    public const SIZE_320_50 = '320x50';
    public const SIZE_300_250 = '300x250';
    public const SIZE_300_600 = '300x600';

    public string $slot;
    public string $size = self::SIZE_728_90;

    /** @return string */
    public function run()
    {
        if (self::isDisabled()) {
            return '';
        }

        $params = Yii::$app->params['adsense'];
        if (!is_array($params)) {
            return '';
        }

        if (!isset($params['slots'][$this->slot])) {
            return '';
        }

        if (YII_ENV_PROD) {
            AdSenseAsset::register($this->view);
            return implode('', [
                Html::tag('ins', '', [
                    'class' => 'adsbygoogle',
                    'data' => [
                        'ad-client' => $params['client'],
                        'ad-slot' => $params['slots'][$this->slot],
                    ],
                    'style' => $this->makeStyles($this->size),
                ]),
                Html::tag(
                    'script',
                    '(adsbygoogle=window.adsbygoogle||[]).push({});',
                ),
            ]);
        } else {
            return Html::tag('ins', Html::img($this->makePlaceholder($this->size)), [
                'class' => 'adsbygoogle',
                'style' => $this->makeStyles($this->size),
            ]);
        }
    }

    /**
     * @return array<string, string>|null
     */
    private function makeStyles(string $sizeTag): ?array
    {
        if (preg_match('/^(\d+)x(\d+)$/', $sizeTag, $match)) {
            return [
                'display' => 'inline-block',
                'width' => "{$match[1]}px",
                'height' => "{$match[2]}px",
            ];
        }

        return null;
    }

    private function makePlaceholder(string $sizeTag): string
    {
        if (preg_match('/^(\d+)x(\d+)$/', $sizeTag, $match)) {
            return vsprintf('https://placehold.jp/%dx%d.png', [
                (int)$match[1],
                (int)$match[2],
            ]);
        }

        return 'about:blank';
    }

    public static function isDisabled(): bool
    {
        $config = Yii::$app->request->cookies->getValue('ads-config');
        return is_string($config) && $config === 'disabled';
    }
}
