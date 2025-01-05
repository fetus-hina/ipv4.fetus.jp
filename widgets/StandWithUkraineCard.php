<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\widgets;

use yii\base\Widget;
use yii\helpers\Html;

final class StandWithUkraineCard extends Widget
{
    public function run(): string
    {
        return Html::tag(
            'aside',
            $this->renderCard(),
            [
                'class' => [
                    'mb-4',
                ],
            ],
        );
    }

    private function renderCard(): string
    {
        return Html::tag(
            'div',
            $this->renderCardBody(),
            [
                'class' => [
                    'card',
                    'border-primary',
                ],
            ],
        );
    }

    private function renderCardBody(): string
    {
        return Html::tag(
            'div',
            StandWithUkraine::widget([
                'marginClass' => 'mb-0',
            ]),
            [
                'class' => [
                    'card-body',
                ],
            ],
        );
    }
}
