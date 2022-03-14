<?php

declare(strict_types=1);

namespace app\widgets;

use LogicException;
use Yii;
use app\models\KrfilterRegion;
use app\models\Region;
use yii\base\Widget;
use yii\helpers\Html;

class Krfilter extends Widget
{
    protected const RUFILTER_ID = 5; // FIXME: constant
    protected const CARD_COLOR = 'info';

    public ?Region $region = null;

    public function run(): string
    {
        if (!$region = $this->region) {
            throw new LogicException();
        }

        if (!$this->shouldBeDisplayed($region)) {
            return '';
        }

        return $this->renderWidget();
    }

    protected function shouldBeDisplayed(Region $region): bool
    {
        $rufilter = KrfilterRegion::find()
            ->andWhere([
                'krfilter_id' => self::RUFILTER_ID,
                'region_id' => $region->id,
            ])
            ->exists();
        if ($rufilter) {
            return false;
        }

        return KrfilterRegion::find()
            ->andWhere(['region_id' => $region->id])
            ->exists();
    }

    protected function renderWidget(): string
    {
        return Html::tag(
            'aside',
            $this->renderCard(),
            [
                'class' => [
                    'col-12',
                    'mb-4',
                ],
            ],
        );
    }

    protected function renderCard(): string
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
                    'border-' . self::CARD_COLOR,
                ],
            ],
        );
    }

    protected function renderCardHeader(): string
    {
        return Html::tag(
            'div',
            Html::encode('krfilter / eufilter'),
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
            $this->getCardBodyTexts(),
            [
                'class' => 'card-body',
            ],
        );
    }

    protected function getCardBodyTexts(): string
    {
        return implode('', [
            Html::tag(
                'p',
                Yii::t(
                    'app',
                    'We have a list of countries that we think are frequently used for denied access lists.',
                ),
            ),
            Html::tag(
                'p',
                Html::a(
                    Yii::t('app', 'For more information, please click here.'),
                    ['krfilter/view']
                ),
                [
                    'class' => [
                        'mb-0',
                    ],
                ],
            ),
        ]);
    }
}
