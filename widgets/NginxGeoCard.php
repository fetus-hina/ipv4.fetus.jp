<?php

declare(strict_types=1);

namespace app\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

use function implode;

final class NginxGeoCard extends Widget
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
            Yii::t('app/nginx-geo', 'Entire list for Nginx Geo'),
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
                        'app/nginx-geo',
                        'You can determine from which country the visitor is coming.',
                    ),
                ),
                // phpcs:enable Generic.Files.LineLength.TooLong
                Html::tag(
                    'div',
                    Html::a(
                        implode(' ', [
                            Html::encode(Yii::t('app', 'Download')),
                            Icon::download(),
                        ]),
                        ['nginx-geo/index'],
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
