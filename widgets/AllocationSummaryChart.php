<?php

declare(strict_types=1);

namespace app\widgets;

use app\assets\AllocationSummaryAsset;
use app\helpers\TypeHelper;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

final class AllocationSummaryChart extends Widget
{
    public function run(): string
    {
        $id = TypeHelper::shouldBeString($this->id);

        if (($v = $this->view) instanceof View) {
            AllocationSummaryAsset::register($v);
            $v->registerJs(\vsprintf('jQuery(%s).chartAllocationSummary();', [
                Json::encode(\vsprintf('#%s-canvas', [
                    $id,
                ])),
            ]));
        }

        return Html::tag(
            'div',
            Html::tag('canvas', '', [
                'class' => [
                    'w-100',
                ],
                'id' => \sprintf('%s-canvas', $id),
            ]),
            [
                'class' => [
                    'w-100',
                    'ratio',
                    'ratio-1x1',
                ],
                'id' => $id,
            ],
        );
    }
}
