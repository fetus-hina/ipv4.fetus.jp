<?php

declare(strict_types=1);

namespace app\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

use function implode;

final class Ipv4byccCard extends Widget
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
            implode('', [
                // phpcs:disable Generic.Files.LineLength.TooLong
                Html::tag(
                    'p',
                    Yii::t(
                        'app',
                        'The output is in the format compatible with <a href="{url}" target="_blank" rel="noopener">this web site</a>.',
                        [
                            'url' => 'http://nami.jp/ipv4bycc/',
                        ],
                    ),
                ),
                // phpcs:enable Generic.Files.LineLength.TooLong
                Html::tag(
                    'div',
                    Html::a(
                        implode(' ', [
                            Html::encode(Yii::t('app/ipv4bycc', 'CIDR format')),
                            Icon::download(),
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
                        implode(' ', [
                            Html::encode(Yii::t('app/ipv4bycc', 'Subnet Mask format')),
                            Icon::download(),
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
}
