<?php

declare(strict_types=1);

namespace app\widgets\ads;

use Yii;
use app\widgets\AdSenseWidget;
use yii\base\Widget;
use yii\helpers\Html;

final class TopAd extends Widget
{
    public function run(): string
    {
        if (!Yii::$app->params['adsense']) {
            return '';
        }

        return Html::tag(
            'div',
            implode('', [
                Html::tag(
                    'div',
                    AdSenseWidget::widget([
                        'slot' => 'pc-top',
                        'size' => AdSenseWidget::SIZE_728_90,
                    ]),
                    [
                        'class' => [
                            'd-none',
                            'd-md-block',
                        ],
                    ],
                ),
                Html::tag(
                    'div',
                    AdSenseWidget::widget([
                        'slot' => 'sp-top',
                        'size' => AdSenseWidget::SIZE_320_50,
                    ]),
                    [
                        'class' => [
                            'd-block',
                            'd-md-none',
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
