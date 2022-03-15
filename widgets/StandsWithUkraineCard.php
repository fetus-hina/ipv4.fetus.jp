<?php

declare(strict_types=1);

namespace app\widgets;

use yii\base\Widget;
use yii\helpers\Html;

final class StandsWithUkraineCard extends Widget
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
            StandsWithUkraine::widget([
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
