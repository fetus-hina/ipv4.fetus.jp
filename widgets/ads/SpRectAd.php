<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\widgets\ads;

use Yii;
use app\widgets\AdSenseWidget;
use yii\base\Widget;
use yii\helpers\Html;

final class SpRectAd extends Widget
{
    public function run(): string
    {
        if (
            AdSenseWidget::isDisabled() ||
            !Yii::$app->params['adsense'] ||
            !isset(Yii::$app->params['adsense']['slots']['sp-rect'])
        ) {
            return '';
        }

        return Html::tag(
            'div',
            AdSenseWidget::widget([
                'slot' => 'sp-rect',
                'size' => AdSenseWidget::SIZE_300_250,
            ]),
            [
                'class' => [
                    'col-12',
                    'd-md-none',
                    'mb-4',
                    'text-center',
                ],
            ],
        );
    }
}
