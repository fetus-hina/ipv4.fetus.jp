<?php

declare(strict_types=1);

use yii\helpers\Html;

$controller = Yii::$app->controller;
$pageId = vsprintf('%s/%s', [
  $controller->id,
  $controller->action->id ?? 'UNKNOWN'
]);

?>
<?php if ((Yii::$app->params['authorWebsite'] ?? '') === 'https://fetus.jp/') { ?>
<header>
  <div class="container">
    <h1>
      <?= Html::a(
        Html::encode('fetus'),
        ($pageId === 'site/index')
          ? Yii::$app->params['authorWebsite']
          : ['site/index']
      ) . "\n" ?>
    </h1>
  </div>
</header>
<?php } ?>
