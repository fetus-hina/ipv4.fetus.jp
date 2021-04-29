<?php

declare(strict_types=1);

use app\models\SearchForm;
use app\widgets\SnsWidget;
use yii\web\View;

/**
 * @var SearchForm $search
 * @var View $this
 */

$this->title = Yii::$app->name . ' : 国/地域別IPアドレス(IPv4アドレス)割り振り（割り当て）一覧';

?>
<main>
  <h1 class="mb-4">
    国/地域別IPアドレス割り振り<span class="d-none">（割り当て）</span>一覧
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
    </div>
  </div>
</main>
