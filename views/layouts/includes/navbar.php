<?php

declare(strict_types=1);

use yii\bootstrap5\BootstrapPluginAsset;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */

BootstrapPluginAsset::register($this);

?>
<div class="container">
  <nav class="navbar navbar-dark bg-dark navbar-expand-md shadow rounded px-3">
    <?= Html::a(Html::encode(Yii::$app->name), ['site/index'], ['class' => 'navbar-brand']) . "\n" ?>
    <?= Html::tag(
      'button',
      Html::tag('span', '', ['class' => 'navbar-toggler-icon']),
      [
        'class' => 'navbar-toggler',
        'type' => 'button',
        'data' => [
          'bs-target' => '#navbarLinks',
          'bs-toggle' => 'collapse',
        ],
        'aria' => [
          'controls' => 'navbarLinks',
          'expanded' => 'true',
          'label' => 'ナビゲーションを開く/閉じる',
        ],
      ]
    ) . "\n" ?>
    <div class="collapse navbar-collapse" id="navbarLinks">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><?= Html::a(
          Html::encode('トップ'),
          ['site/index'],
          ['class' => 'nav-link']
        ) ?></li>
        <li class="nav-item"><?= Html::a(
          Html::encode('krfilter / eufilter (for GDPR)'),
          ['krfilter/view'],
          ['class' => 'nav-link']
        ) ?></li>
      </ul>
    </div>
  </nav>
</div>
