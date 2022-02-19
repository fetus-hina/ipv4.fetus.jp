<?php

declare(strict_types=1);

use app\widgets\LanguageButton;
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
          'label' => Yii::t('app/navbar', 'Toggle Navbar'),
        ],
      ]
    ) . "\n" ?>
    <div class="collapse navbar-collapse" id="navbarLinks">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><?= Html::a(
          Html::encode(Yii::t('app/navbar', 'Top')),
          ['site/index'],
          ['class' => 'nav-link']
        ) ?></li>
        <li class="nav-item"><?= Html::a(
          Html::encode(Yii::t('app/navbar', 'krfilter / eufilter (for GDPR)')),
          ['krfilter/view'],
          ['class' => 'nav-link']
        ) ?></li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown">
          <?= LanguageButton::widget() . "\n" ?>
        </li>
      </ul>
    </div>
  </nav>
</div>
