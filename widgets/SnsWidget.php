<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\widgets;

use Yii;
use app\widgets\sns\Hatebu;
use app\widgets\sns\Twitter;
use yii\base\Widget;
use yii\helpers\Html;

use function implode;
use function preg_match;

final class SnsWidget extends Widget
{
    public function run(): string
    {
        if (AdSenseWidget::isDisabled()) {
            return '';
        }

        return Html::tag(
            'div',
            implode('', [
                Twitter::widget(),
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
            ],
        );
    }

    private function renderHatebu(): string
    {
        if (!preg_match('/^ja\b/', Yii::$app->language)) {
            return '';
        }

        return Hatebu::widget();
    }
}
