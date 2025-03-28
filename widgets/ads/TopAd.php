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

use function implode;

final class TopAd extends Widget
{
    public function run(): string
    {
        if (
            AdSenseWidget::isDisabled() ||
            !Yii::$app->params['adsense']
        ) {
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
