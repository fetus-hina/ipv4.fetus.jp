<?php

declare(strict_types=1);

namespace app\widgets\ads;

use Yii;
use app\widgets\AdSenseWidget;
use yii\base\Widget;
use yii\helpers\Html;

use function implode;

final class SideAd extends Widget
{
    public function run(): string
    {
        if (!Yii::$app->params['adsense']) {
            return '';
        }

        return Html::tag(
            'aside',
            implode('', [
                Html::tag(
                    'div',
                    AdSenseWidget::widget([
                        'slot' => 'pc-side',
                        'size' => AdSenseWidget::SIZE_300_250,
                    ]),
                    [
                        'class' => [
                            'd-none',
                            'd-sm-block',
                        ],
                    ],
                ),
            ]),
            [
                'class' => [
                    'mb-4',
                    'text-center',
                ],
                'style' => [
                    'margin-left' => '-.75rem',
                    'margin-right' => '-.75rem',
                ],
            ],
        );
    }
}
