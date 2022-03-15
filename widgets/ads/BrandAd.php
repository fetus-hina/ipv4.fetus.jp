<?php

declare(strict_types=1);

namespace app\widgets\ads;

use Yii;
use app\widgets\AdSenseWidget;
use yii\base\Widget;
use yii\helpers\Html;

final class BrandAd extends Widget
{
    public function run(): string
    {
        if (
            !Yii::$app->params['adsense'] ||
            !isset(Yii::$app->params['adsense']['slots']['pc-brand'])
        ) {
            return '';
        }

        return Html::tag(
            'div',
            Html::tag(
                'div',
                AdSenseWidget::widget([
                    'slot' => 'pc-brand',
                    'size' => AdSenseWidget::SIZE_728_90,
                ]),
                [
                    'class' => [
                        'd-none',
                        'd-lg-block',
                    ],
                ],
            ),
            [
                'class' => [
                    'position-absolute',
                ],
                'style' => [
                    'right' => '15px',
                    'top' => 'calc(-0.3090169944rem + 5px)',
                ],
            ],
        );
    }
}
