<?php

declare(strict_types=1);

use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

$controller = Yii::$app->controller;
$pageId = vsprintf('%s/%s', [
  $controller->id,
  $controller->action->id ?? 'UNKNOWN'
]);

?>
<?php if ((Yii::$app->params['authorWebsite'] ?? '') === 'https://fetus.jp/') { ?>
<header><?= Html::tag(
  'div',
  implode('', [
    Html::tag('h1', Html::a(
      Html::encode('fetus'),
      ($pageId === 'site/index')
        ? Yii::$app->params['authorWebsite']
        : ['site/index']
    )),
    $this->render('//layouts/ads/brand'),
  ]),
  [
    'class' => 'container',
    'style' => [
      'position' => 'relative',
    ],
  ]
) ?></header>
<?php } ?>
