<?php

declare(strict_types=1);

use app\widgets\AdSenseWidget;
use yii\helpers\Html;

if (!Yii::$app->params['adsense']) {
  return;
}

echo Html::tag(
  'aside',
  implode('', [
    Html::tag(
      'div',
      AdSenseWidget::widget(['slot' => 'pc-side', 'size' => AdSenseWidget::SIZE_300_250]),
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
