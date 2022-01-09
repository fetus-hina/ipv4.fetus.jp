<?php

declare(strict_types=1);

use yii\helpers\Html;

echo Html::tag(
  'div',
  implode(' ', [
    Yii::t('app', 'National flags of the countries are displayed using {source}.', [
      'source' => Html::a(Html::encode('flag-icons'), 'https://flagicons.lipis.dev/', [
        'target' => 'blank',
        'rel' => 'noopener noreferrer',
      ]),
    ]),
    Yii::t('app', 'You may not see the latest flag, or you may see the incorrect flag.'),
  ]),
  [
    'class' => 'small',
    'lang' => 'en',
  ],
) . "\n";
