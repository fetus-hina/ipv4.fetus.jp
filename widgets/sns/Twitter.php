<?php

declare(strict_types=1);

namespace app\widgets\sns;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\web\View;

final class Twitter extends Widget
{
    public function run(): string
    {
        if ($this->view instanceof View) {
            $this->view->registerJsFile('https://platform.twitter.com/widgets.js', [
                'async' => true,
                'charset' => 'UTF-8',
                'position' => View::POS_END,
            ]);
        }

        return Html::tag(
            'span',
            Html::a(
                Html::encode(
                    Yii::t('app', 'Tweet'),
                ),
                'https://twitter.com/share',
                [
                    'class' => [
                        'twitter-share-button',
                    ],
                    'data' => [
                        'show-count' => 'false',
                    ],
                ],
            ),
            [
                'class' => [
                    'me-1',
                ],
            ],
        );
    }
}
