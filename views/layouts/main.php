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
    <link type="image/svg+xml" href="/favicon/favicon.svg" rel="icon" sizes="any">
    <link type="image/png" href="/favicon/apple-touch-icon-57.png" rel="apple-touch-icon" sizes="57x57">
    <link type="image/png" href="/favicon/apple-touch-icon-60.png" rel="apple-touch-icon" sizes="60x60">
    <link type="image/png" href="/favicon/apple-touch-icon-72.png" rel="apple-touch-icon" sizes="72x72">
    <link type="image/png" href="/favicon/apple-touch-icon-76.png" rel="apple-touch-icon" sizes="76x76">
    <link type="image/png" href="/favicon/apple-touch-icon-114.png" rel="apple-touch-icon" sizes="114x114">
    <link type="image/png" href="/favicon/apple-touch-icon-120.png" rel="apple-touch-icon" sizes="120x120">
    <link type="image/png" href="/favicon/apple-touch-icon-144.png" rel="apple-touch-icon" sizes="144x144">
    <link type="image/png" href="/favicon/apple-touch-icon-152.png" rel="apple-touch-icon" sizes="152x152">
    <link type="image/png" href="/favicon/apple-touch-icon-180.png" rel="apple-touch-icon" sizes="180x180">
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
