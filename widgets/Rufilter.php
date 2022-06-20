<?php

declare(strict_types=1);

namespace app\widgets;

use app\models\KrfilterRegion;
use app\models\Region;
use yii\helpers\Html;

final class Rufilter extends Krfilter
{
    protected function shouldBeDisplayed(Region $region): bool
    {
        return KrfilterRegion::find()
            ->andWhere([
                'krfilter_id' => self::RUFILTER_ID,
                'region_id' => $region->id,
            ])
            ->exists();
    }

    protected function renderCardHeader(): string
    {
        return Html::tag(
            'div',
            Html::encode('rufilter'),
            [
                'class' => [
                    'card-header',
                    'text-white',
                    'bg-' . self::CARD_COLOR,
                ],
            ],
        );
    }

    protected function renderCardBody(): string
    {
        return Html::tag(
            'div',
            \implode('', [
                StandWithUkraine::widget(),
                $this->getCardBodyTexts(),
            ]),
            [
                'class' => 'card-body',
            ],
        );
    }
}
