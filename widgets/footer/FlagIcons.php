<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\widgets\footer;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

use function implode;

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
                            'rel' => 'noopener',
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
