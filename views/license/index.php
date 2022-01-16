<?php

declare(strict_types=1);

use app\helpers\TypeHelper;
use yii\helpers\Html;

?>
<h2><?= Yii::t('app/license', 'Open Source Licenses') ?></h2>
<p>
  <?= Yii::t('app/license', 'This website uses the artifacts of these open source projects.') ?>
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
</ul>
<hr>
<h3><?= Yii::$app->name ?></h3>
<div class="card ms-4">
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

<hr>
<h3>
  <?= Yii::t('app/license', 'Application Template ({name})', [
    'name' => 'yiisoft/yii2-app-basic',
  ]) . "\n" ?>
</h3>
<div class="card ms-4">
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
