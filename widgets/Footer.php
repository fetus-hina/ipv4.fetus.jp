<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\widgets;

use Yii;
use app\widgets\footer\Copyright;
use app\widgets\footer\DatabaseTimestamp;
use app\widgets\footer\FlagIcons;
use app\widgets\footer\RepoLink;
use yii\base\Widget;
use yii\helpers\Html;

use function implode;

final class Footer extends Widget
{
    public function run(): string
    {
        return Html::tag(
            'footer',
            Html::tag(
                'div',
                implode('', [
                    Copyright::widget(),
                    RepoLink::widget(),
                    $this->renderLicenseLink(),
                    DatabaseTimestamp::widget(),
                    FlagIcons::widget(),
                ]),
                [
                    'class' => [
                        'container',
                    ],
                ],
            ),
            [],
        );
    }

    private function renderLicenseLink(): string
    {
        return Html::tag(
            'div',
            Html::a(
                Html::encode(Yii::t('app', 'Open Source Licenses')),
                ['license/index'],
                [],
            ),
            [
                'class' => [
                    'small',
                ],
            ],
        );
    }
}
