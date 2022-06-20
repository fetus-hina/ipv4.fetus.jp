<?php

declare(strict_types=1);

namespace app\widgets;

use Yii;
use app\assets\BootstrapIconsAsset;
use yii\base\Widget;
use yii\helpers\Html;
use yii\web\View;

final class Ipv4byccCard extends Widget
{
    public function run(): string
    {
        if (($view = $this->view) instanceof View) {
            BootstrapIconsAsset::register($view);
        }

        return Html::tag(
            'div',
            \implode('', [
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
            Yii::t('app/ipv4bycc', 'ipv4bycc Compatible Format Data'),
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
            \implode('', [
                // phpcs:disable Generic.Files.LineLength.TooLong
                Html::tag(
                    'p',
                    Yii::t(
                        'app',
                        'The output is in the format compatible with <a href="{url}" target="_blank" rel="nofollow noopener">this web site</a>.',
                        [
                            'url' => 'http://nami.jp/ipv4bycc/',
                        ],
                    ),
                ),
                // phpcs:enable Generic.Files.LineLength.TooLong
                Html::tag(
                    'div',
                    Html::a(
                        \implode(' ', [
                            Html::encode(Yii::t('app/ipv4bycc', 'CIDR format')),
                            Html::tag('span', '', ['class' => 'bi bi-download']),
                        ]),
                        ['ipv4bycc/cidr'],
                        [
                            'class' => 'btn btn-primary',
                            'hreflang' => 'ja',
                            'rel' => 'nofollow',
                            'type' => 'text/plain',
                        ],
                    ),
                    [
                        'class' => 'd-grid mb-2',
                    ],
                ),
                Html::tag(
                    'div',
                    Html::a(
                        \implode(' ', [
                            Html::encode(Yii::t('app/ipv4bycc', 'Subnet Mask format')),
                            Html::tag('span', '', ['class' => 'bi bi-download']),
                        ]),
                        ['ipv4bycc/mask'],
                        [
                            'class' => 'btn btn-primary',
                            'hreflang' => 'ja',
                            'rel' => 'nofollow',
                            'type' => 'text/plain',
                        ],
                    ),
                    [
                        'class' => 'd-grid mb-0',
                    ],
                ),
            ]),
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
        $joiner = \preg_match('/^ja\b/i', Yii::$app->language)
            ? ''
            : ' ';

        // phpcs:disable Generic.Files.LineLength.TooLong
        return [
            \implode($joiner, [
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
