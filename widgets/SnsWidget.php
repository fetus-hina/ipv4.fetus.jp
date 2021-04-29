<?php

declare(strict_types=1);

namespace app\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use yii\web\View;

final class SnsWidget extends Widget
{
    /** @return string */
    public function run()
    {
        return Html::tag(
            'div',
            implode('', [
                $this->renderTwitter(),
                $this->renderHatebu(),
            ]),
            [
                'aria' => [
                    'hidden' => 'true',
                ],
                'style' => [
                    'line-height' => '1px',
                    'height' => '20px',
                ],
            ]
        );
    }

    private function renderTwitter(): string
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
                Html::encode('Tweet'),
                'https://twitter.com/share',
                [
                    'class' => 'twitter-share-button',
                    'data-show-count' => 'false',
                ]
            ),
            ['class' => 'mr-1'],
        );
    }

    private function renderHatebu(): string
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
                    'title' => 'このエントリーをはてなブックマークに追加',
                    'data' => [
                        'hatena-bookmark-layout' => 'basic-label-counter',
                        'hatena-bookmark-lang' => 'ja',
                    ],
                ]
            ),
            ['class' => 'mr-1'],
        );
    }
}
