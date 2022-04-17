<?php

declare(strict_types=1);

use app\helpers\ApplicationLanguage;
use app\helpers\TypeHelper;
use yii\helpers\Html;

ApplicationLanguage::registerLink(Yii::$app, ['license/index']);

?>
<h2><?= Yii::t('app/license', 'Open Source Licenses') ?></h2>
<p>
  <?= Yii::t('app/license', 'This website uses the artifacts of these open source projects.') ?>
  <?= Yii::t('app/license', 'Many thanks to all involved.') ?>
</p>
<ul>
  <li>
    <?= Html::a(
      Yii::t('app/license', 'Composer Packages'),
      ['license/composer'],
    ) . "\n" ?>
  </li>
  <li>
    <?= Html::a(
      Yii::t('app/license', 'NPM Packages'),
      ['license/npm'],
    ) . "\n" ?>
  </li>
  <li>
    <?= Html::a(
      Yii::t('app/license', 'Fonts'),
      ['license/font'],
    ) . "\n" ?>
  </li>
</ul>
<hr>
<h3><?= Html::encode(Yii::$app->name) ?></h3>
<div class="card ms-4 mb-4">
  <div class="card-body">
    <?= Html::tag(
      'pre',
      Html::encode(
        (string)@file_get_contents(
          TypeHelper::shouldBeString(Yii::getAlias('@app/LICENSE')),
        ),
      ),
      ['class' => 'm-0 fs-6 lh-sm']
    ) . "\n" ?>
  </div>
</div>
<h3>
  <?= Yii::t('app/license', 'Application Template ({name})', [
    'name' => 'yiisoft/yii2-app-basic',
  ]) . "\n" ?>
</h3>
<div class="card ms-4 mb-4">
  <div class="card-body">
    <?= Html::tag(
      'pre',
      Html::encode(
        (string)@file_get_contents(
          TypeHelper::shouldBeString(Yii::getAlias('@app/LICENSE.app-template.md')),
        ),
      ),
      ['class' => 'm-0 fs-6 lh-sm']
    ) . "\n" ?>
  </div>
</div>
