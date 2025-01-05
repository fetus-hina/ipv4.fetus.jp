<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\widgets;

use yii\base\Widget;
use yii\helpers\Html;

final class ContentWrapper extends Widget
{
    public string $content = '';

    public function run(): string
    {
        return Html::tag(
            'div',
            Html::tag(
                'div',
                Html::tag(
                    'div',
                    $this->content,
                    [
                        'class' => [
                            'container',
                        ],
                    ],
                ),
                [
                    'class' => [
                        'mb-1',
                    ],
                ],
            ),
            [
                'class' => [
                    'flex-grow-1',
                ],
            ],
        );
    }
}
