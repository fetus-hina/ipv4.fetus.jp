<?php

declare(strict_types=1);

namespace app\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

final class AboutUsCard extends Widget
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
            Yii::t('app/about', 'About Us'),
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
        $paragraphs = $this->getParagraphs();

        return Html::tag(
            'div',
            implode('', array_map(
                fn (string $html, int $index, int $lastIndex) => Html::tag(
                    'p',
                    $html,
                    [
                        'class' => $index === $lastIndex
                            ? [
                                'mb-0',
                            ]
                            : [],
                    ],
                ),
                array_values($paragraphs),
                range(0, count($paragraphs) - 1, 1),
                array_fill(0, count($paragraphs), count($paragraphs) - 1),
            )),
            [
                'class' => [
                    'card-body',
                ],
            ],
        );
    }

    private function getParagraphs(): array
    {
        // 日本語ではそのまま結合、その他（英語）ではスペース区切り
        $joiner = preg_match('/^ja\b/i', Yii::$app->language)
            ? ''
            : ' ';

        // phpcs:disable Generic.Files.LineLength.TooLong
        return [
            implode($joiner, [
                Yii::t(
                    'app/about',
                    'Information is not guaranteed.',
                ),
                Yii::t(
                    'app/about',
                    'We are not responsible for any damage caused by the use of the information on this site.',
                ),
            ]),
            Yii::t(
                'app/about',
                'The information is updated automatically every day by retrieving it from the Regional Internet Registry.',
            ),
            Html::a(
                Yii::t('app/about', 'Click here for more information.'),
                ['site/about'],
            ),
            Yii::t('app/about', 'For automated access, see the page above.'),
            Html::a(
                Yii::t('app/about', 'For specifications about downloadable formats, please click here.'),
                ['site/schema'],
            ),
        ];
        // phpcs:enable Generic.Files.LineLength.TooLong
    }
}
