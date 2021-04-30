<?php

declare(strict_types=1);

use app\widgets\AdSenseWidget;
use yii\helpers\Html;

if (
  !Yii::$app->params['adsense'] ||
  !isset(Yii::$app->params['adsense']['slots']['sp-rect'])
) {
  return;
}

echo Html::tag(
  'div',
  AdSenseWidget::widget(['slot' => 'sp-rect', 'size' => AdSenseWidget::SIZE_300_250]),
  ['class' => 'col-12 d-md-none mb-4 text-center']
);
echo "\n";
