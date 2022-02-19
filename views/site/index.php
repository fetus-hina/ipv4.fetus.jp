<?php

declare(strict_types=1);

use app\helpers\ApplicationLanguage;
use app\models\SearchForm;
use app\widgets\SnsWidget;
use yii\web\View;

/**
 * @var SearchForm $search
 * @var View $this
 */

$this->title = Yii::t('app', '{siteName}: IPv4 Address Allocation List for Each Country/Region', [
    'siteName' => Yii::$app->name,
]);

$metas = [
    'description' => strip_tags(Yii::t('app', 'IPv4 Address Allocation List for Each Country/Region')),
    'keywords' => implode(',', [
        'IPv4',
        'IP Address',
        'IPアドレス',
        'IPv4 Address',
        'IPv4 Allocation',
        'IPv4 Assign',
        'IPv4アドレス',
        'IPv4割り振り',
        'IPv4割り当て',
        '国別IP',
    ]),
];
foreach ($metas as $name => $value) {
    $this->registerMetaTag(['name' => $name, 'content' => $value]);
}

ApplicationLanguage::registerLink(Yii::$app, ['site/index']);

?>
<main>
  <h1 class="mb-4">
    <?= Yii::t('app', 'IPv4 Address Allocation List for Each Country/Region') . "\n" ?>
  </h1>
  <aside class="mb-0">
    <?= SnsWidget::widget() . "\n" ?>
  </aside>
  <hr>
  <?= $this->render('//layouts/ads/top') . "\n" ?>
  <div class="row">
    <div class="col-12 d-lg-none">
      <aside class="mb-4">
        <?= $this->render('//site/index/search', ['form' => $search]) . "\n" ?>
      </aside>
    </div>
    <?= $this->render('//layouts/ads/sp-rect') . "\n" ?>
    <div class="col-12 col-lg-8">
      <div class="mb-4">
        <?= $this->render('//site/index/main') . "\n" ?>
      </div>
    </div>
    <div class="col-12 col-lg-4">
      <aside class="mb-4 d-none d-lg-block">
        <?= $this->render('//site/index/search', ['form' => $search]) . "\n" ?>
      </aside>
      <?= $this->render('//layouts/ads/side') . "\n" ?>
      <aside class="mb-4">
        <?= $this->render('//site/index/about') . "\n" ?>
      </aside>
      <?= $this->render('//layouts/ads/side-skyscraper') . "\n" ?>
    </div>
  </div>
</main>
