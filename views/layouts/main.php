<?php

declare(strict_types=1);

use app\assets\AppAsset;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var string $content
 */

AppAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<?= Html::beginTag('html', [
  'class' => 'h-100',
  'lang' => Yii::$app->language,
]) . "\n" ?>
  <head>
    <?= Html::tag('meta', '', ['charset' => Yii::$app->charset]) . "\n" ?>
    <?= Html::tag('meta', '', [
      'name' => 'viewport',
      'content' => 'width=device-width,initial-scale=1,shrink-to-fit=no',
    ]) . "\n" ?>
    <?= Html::tag('meta', '', [
      'http-equiv' => 'X-UA-Compatible',
      'content' =>  'IE=edge',
    ]) . "\n" ?>
    <?php $this->registerCsrfMetaTags(); echo "\n" ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head(); echo "\n" ?>
  </head>
  <body class="h-100">
<?php $this->beginBody() ?>
    <div class="d-flex justify-content-start flex-column h-100">
      <div class="mb-4">
        <?= $this->render('//layouts/includes/fetus-header') . "\n" ?>
      </div>
      <div class="mb-4">
        <?= $this->render('//layouts/includes/navbar') . "\n" ?>
      </div>
      <div class="flex-grow-1">
        <div class="mb-4">
          <div class="container">
            <?= $content ?><?= "\n" ?>
          </div>
        </div>
      </div>
      <div>
        <?= $this->render('//layouts/includes/footer') . "\n" ?>
      </div>
    </div>
<?php $this->endBody() ?>
  </body>
</html>
<?php $this->endPage() ?>
