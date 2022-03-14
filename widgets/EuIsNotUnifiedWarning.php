<?php

declare(strict_types=1);

namespace app\widgets;

use LogicException;
use Yii;
use app\models\Region;
use yii\base\Widget;
use yii\helpers\Html;

final class EuIsNotUnifiedWarning extends Widget
{
    public ?Region $region = null;

    public function run(): string
    {
        if (!$region = $this->region) {
            throw new LogicException();
        }

        if ($region->id !== 'eu') {
            return '';
        }

        return Html::tag(
            'aside',
            $this->renderCard(),
            [
                'class' => 'mb-4',
            ],
        );
    }

    private function renderCard(): string
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
                    'border-danger',
                ],
            ],
        );
    }

    private function renderCardHeader(): string
    {
        return Html::tag(
            'div',
            Yii::t('app', 'Attention'),
            [
                'class' => [
                    'card-header',
                    'bg-danger',
                    'text-white',
                ],
            ],
        );
    }

    private function renderCardBody(): string
    {
        $texts = [
            Yii::t(
                'app',
                'This list is NOT a complete list of IP addresses allocated to the entire European region.',
            ),
            Yii::t(
                'app',
                'Most IP addresses in the European region are allocated to individual countries.',
            ),
            Yii::t(
                'app',
                'Refer to {eufilter} if you need the whole integrated list.',
                [
                    'eufilter' => Html::a(
                        'eufilter',
                        ['krfilter/view'],
                    ),
                ],
            ),
        ];

        return Html::tag(
            'div',
            implode('', array_map(
                fn (string $html, int $index, int $finalIndex): string => Html::tag(
                    'p',
                    $html,
                    [
                        'class' => $index === $finalIndex
                            ? 'mb-0'
                            : null,
                    ],
                ),
                array_values($texts),
                range(0, count($texts) - 1, 1),
                array_fill(0, count($texts), count($texts) - 1),
            )),
            [
                'class' => [
                    'card-body',
                ],
            ],
        );
    }
}
