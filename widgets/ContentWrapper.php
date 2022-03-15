<?php

declare(strict_types=1);

namespace app\widgets;

use yii\base\Widget;
use yii\helpers\Html;

final class ContentWrapper extends Widget
{
    public string $content = '';

    public function run(): string
    {
        return Html::tag(
            'div',
            Html::tag(
                'div',
                Html::tag(
                    'div',
                    $this->content,
                    [
                        'class' => [
                            'container',
                        ],
                    ],
                ),
                [
                    'class' => [
                        'mb-1',
                    ],
                ],
            ),
            [
                'class' => [
                    'flex-grow-1',
                ],
            ],
        );
    }
}
