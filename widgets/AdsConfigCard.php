<?php

declare(strict_types=1);

namespace app\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

use function array_fill;
use function array_map;
use function array_values;
use function count;
use function implode;
use function preg_match;
use function range;

final class AdsConfigCard extends Widget
{
    public function run(): string
    {
        return Html::tag(
            'div',
            implode('', [
                $this->renderCardHeader(),
                $this->renderCardBody(),
            ]),
            [
                'class' => [
                    'card',
                ],
            ],
        );
    }

    private function renderCardHeader(): string
    {
        return Html::tag(
            'div',
            Yii::t('app/ads', 'Ad-Blocking Tools'),
            [
                'class' => [
                    'bg-primary',
                    'card-header',
                    'text-white',
                ],
            ],
        );
    }

    private function renderCardBody(): string
    {
        return Html::tag(
            'div',
            implode('', [
                $this->renderTexts(),
                $this->renderButtons(),
            ]),
            [
                'class' => [
                    'card-body',
                ],
            ],
        );
    }

    private function renderTexts(): string
    {
        $paragraphs = $this->getParagraphs();

        return Html::tag(
            'div',
            implode('', array_map(
                fn (string $html, int $index, int $lastIndex) => Html::tag(
                    'p',
                    $html,
                    [
                        'class' => $index === $lastIndex
                            ? ['mb-0']
                            : ['mb-3'],
                    ],
                ),
                array_values($paragraphs),
                range(0, count($paragraphs) - 1, 1),
                array_fill(0, count($paragraphs), count($paragraphs) - 1),
            )),
            ['class' => 'mb-4'],
        );
    }

    /**
     * @return string[]
     */
    private function getParagraphs(): array
    {
        // 日本語ではそのまま結合、その他（英語）ではスペース区切り
        $joiner = preg_match('/^ja\b/i', Yii::$app->language)
            ? ''
            : ' ';

        // phpcs:disable Generic.Files.LineLength.TooLong
        return [
            Yii::t(
                'app/ads',
                'You are free to use the ad-blocking tool.',
            ),
            Yii::t(
                'app/ads',
                'This website does not change behavior based on whether or not ad-blocking tools are used.',
            ),
            Yii::t(
                'app/ads',
                'However, ad-blocking tools may inhibit necessary access and affect site behavior.',
            ),
            implode($joiner, [
                Yii::t('app/ads', 'You can exclude all ad behavior completely by clicking on the following button.'),
                Yii::t(
                    'app/ads',
                    'You will have the best experience if you click this button and then stop the ad-blocking tool.',
                ),
            ]),
        ];
        // phpcs:enable Generic.Files.LineLength.TooLong
    }

    private function renderButtons(): string
    {
        return Html::tag(
            'div',
            implode('', [
                Html::a(
                    Yii::t('app/ads', 'Disable Ads'),
                    ['site/disable-ads'],
                    [
                        'class' => [
                            'btn',
                            'btn-outline-primary',
                            'btn-sm',
                        ],
                        'data' => [
                            'method' => 'post',
                        ],
                    ],
                ),
                Html::a(
                    Yii::t('app/ads', 'Enable Ads'),
                    ['site/enable-ads'],
                    [
                        'class' => [
                            'btn',
                            'btn-outline-primary',
                            'btn-sm',
                        ],
                        'data' => [
                            'method' => 'post',
                        ],
                    ],
                ),
            ]),
            [
                'class' => 'btn-group',
                'role' => 'group',
            ],
        );
    }
}
