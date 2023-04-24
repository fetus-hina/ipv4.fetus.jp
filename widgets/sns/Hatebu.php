<?php

declare(strict_types=1);

namespace app\widgets\sns;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\web\View;

use function preg_match;

final class Hatebu extends Widget
{
    public function run(): string
    {
        if ($this->view instanceof View) {
            $this->view->registerJsFile('https://b.st-hatena.com/js/bookmark_button.js', [
                'async' => true,
                'charset' => 'UTF-8',
                'position' => View::POS_END,
            ]);
        }

        return Html::tag(
            'span',
            Html::a(
                Html::img('https://b.st-hatena.com/images/v4/public/entry-button/button-only@2x.png', [
                    'alt' => 'B!',
                    'title' => '',
                    'width' => '20',
                    'height' => '20',
                    'style' => [
                        'border' => '0',
                    ],
                ]),
                'https://b.hatena.ne.jp/entry/',
                [
                    'class' => 'hatena-bookmark-button',
                    'title' => Yii::t('app', 'Add this page to Hatena Bookmark'),
                    'data' => [
                        'hatena-bookmark-layout' => 'basic-label-counter',
                        'hatena-bookmark-lang' => preg_match('/^ja\b/', Yii::$app->language)
                            ? 'ja'
                            : 'en',
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
