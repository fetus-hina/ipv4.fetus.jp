<?php

declare(strict_types=1);

use app\widgets\AdSenseWidget;
use yii\helpers\Html;

if (
  !Yii::$app->params['adsense'] ||
  !isset(Yii::$app->params['adsense']['slots']['pc-side2'])
) {
  return;
}

echo Html::tag(
  'aside',
  implode('', [
    Html::tag(
      'div',
      AdSenseWidget::widget(['slot' => 'pc-side2', 'size' => AdSenseWidget::SIZE_300_600]),
      ['class' => 'd-none d-sm-block']
    ),
  ]),
  [
    'class' => 'mb-4 text-center',
    'style' => [
      'margin-left' => '-.75rem',
      'margin-right' => '-.75rem',
    ],
  ]
);
echo "\n";
