<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

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
use function range;

final class GitAccessCard extends Widget
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
            Yii::t('app/git', 'Get Lists via Git'),
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
            implode(
                '',
                array_map(
                    fn (string $html, int $index, int $lastIndex) => Html::tag(
                        'p',
                        $html,
                        [
                            'class' => $index === $lastIndex ? ['mb-0'] : [],
                        ],
                    ),
                    array_values($paragraphs),
                    range(0, count($paragraphs) - 1, 1),
                    array_fill(0, count($paragraphs), count($paragraphs) - 1),
                ),
            ),
            [
                'class' => ['card-body'],
            ],
        );
    }

    /**
     * @return string[]
     */
    private function getParagraphs(): array
    {
        return [
            Yii::t('app/git', 'The information is also placed on GitHub.'),
            Yii::t('app/git', 'Please refer {repo} if you prefer.', [
                'repo' => Html::a(
                    implode(' ', [
                        Icon::github(),
                        Html::tag('code', Html::encode('fetus-hina/ipv4.fetus.jp-exports')),
                    ]),
                    'https://github.com/fetus-hina/ipv4.fetus.jp-exports',
                    [
                        'rel' => 'noreferer noopener',
                        'target' => '_blank',
                    ],
                ),
            ]),
        ];
    }
}
