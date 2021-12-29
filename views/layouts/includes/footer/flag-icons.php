<?php

declare(strict_types=1);

use yii\helpers\Html;

(function (): void {
  $flagIcons = Html::a(Html::encode('flag-icons'), 'https://flagicons.lipis.dev/', [
    'target' => 'blank',
    'rel' => 'noopener noreferrer',
  ]);

  echo Html::tag(
    'div',
    implode('', [
      '各国の国旗は' . $flagIcons . 'を使用して表示しています。',
      '最新の国旗が表示されていない場合や誤ったデザインの国旗が表示されている場合があります。',
    ]),
    ['class' => 'small'],
  );
  echo Html::tag(
    'div',
    implode(' ', [
      'National flags of the countries are displayed using ' . $flagIcons . '.',
      'You may not see the latest flag, or you may see the incorrect flag.',
    ]),
    ['class' => 'small'],
  );
})();
