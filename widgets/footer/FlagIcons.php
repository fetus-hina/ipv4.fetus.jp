<?php

declare(strict_types=1);

namespace app\widgets\footer;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

final class FlagIcons extends Widget
{
    public function run(): string
    {
        return Html::tag(
            'div',
            implode(' ', [
                Yii::t('app', 'National flags of the countries are displayed using {source}.', [
                    'source' => Html::a(
                        Html::encode('flag-icons'),
                        'https://flagicons.lipis.dev/',
                        [
                            'target' => 'blank',
                            'rel' => 'noopener noreferrer',
                        ],
                    ),
                ]),
                Html::encode(
                    Yii::t('app', 'You may not see the latest flag, or you may see the incorrect flag.'),
                ),
            ]),
            [
                'class' => 'small',
            ],
        );
    }
}
