<?php

declare(strict_types=1);

use app\assets\BootstrapIconsAsset;
use app\helpers\ApplicationLanguage;
use yii\bootstrap5\BootstrapPluginAsset;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */

BootstrapIconsAsset::register($this);
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
          <?= Html::a(
            implode(' ', [
              Html::tag('span', '', ['class' => 'bi bi-translate']),
              Html::encode('Language'),
            ]),
            'javascript:;',
            [
              'aria' => [
                'expanded' => 'false',
              ],
              'class' => 'nav-link dropdown-toggle',
              'data' => [
                'bs-toggle' => 'dropdown',
              ],
              'id' => 'navbar-language-dropdown',
              'role' => 'button',
            ]
          ) . "\n" ?>
          <ul class="dropdown-menu" aria-labelledby="navbar-language-dropdown">
            <li>
              <?= Html::a(
                implode(' ', [
                  Html::encode(Yii::t('app', 'Default') . ' (Default)'),
                ]),
                'javascript:;',
                [
                  'class' => 'dropdown-item language-switcher',
                  'data' => [
                    'language' => 'default',
                  ],
                ],
              ) . "\n" ?>
            </li>
            <li><hr class="dropdown-divider"></li>
<?php foreach (ApplicationLanguage::getValidLanguages() as $langCode => $langName) { ?>
            <li>
              <?= Html::a(
                implode(' ', [
                  preg_match('/^' . preg_quote($langCode) . '\b/i', Yii::$app->language)
                    ? '<span class="bi bi-record-circle-fill"></span>'
                    : '<span class="bi bi-circle"></span>',
                  Html::encode($langName),
                ]),
                'javascript:;',
                [
                  'class' => 'dropdown-item language-switcher',
                  'data' => [
                    'language' => $langCode,
                  ],
                ],
              ) . "\n" ?>
            </li>
<?php } ?>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
</div>
