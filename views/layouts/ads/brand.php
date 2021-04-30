<?php

declare(strict_types=1);

use app\widgets\AdSenseWidget;
use yii\helpers\Html;

if (
  !Yii::$app->params['adsense'] ||
  !isset(Yii::$app->params['adsense']['slots']['pc-brand'])
) {
  return;
}

echo Html::tag(
  'div',
  Html::tag(
    'div',
    AdSenseWidget::widget(['slot' => 'pc-brand', 'size' => AdSenseWidget::SIZE_728_90]),
    ['class' => 'd-none d-lg-block']
  ),
  [
    'style' => [
      'position' => 'absolute',
      'right' => '15px',
      'top' => 'calc(-0.3090169944rem + 5px)',
    ],
  ]
);
echo "\n";
