<?php

declare(strict_types=1);

namespace app\widgets\ads;

use Yii;
use app\widgets\AdSenseWidget;
use yii\base\Widget;
use yii\helpers\Html;

final class SkyscraperAd extends Widget
{
    public function run(): string
    {
        if (
            !Yii::$app->params['adsense'] ||
            !isset(Yii::$app->params['adsense']['slots']['pc-side2'])
        ) {
            return '';
        }

        return Html::tag(
            'aside',
            implode('', [
                Html::tag(
                    'div',
                    AdSenseWidget::widget([
                        'slot' => 'pc-side2',
                        'size' => AdSenseWidget::SIZE_300_600,
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
