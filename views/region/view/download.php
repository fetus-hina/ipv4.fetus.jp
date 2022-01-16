<?php

declare(strict_types=1);

use app\models\DownloadTemplate;
use app\models\MergedCidr;
use app\models\Region;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var Region $region
 */

if (Yii::$app->request->isPjax) {
  return;
}

?>
<aside class="card border-primary">
  <div class="card-header bg-primary text-white">
    <?= Yii::t('app', 'Download') . "\n" ?>
  </div>
  <div class="card-body">
    <div class="text-muted">
      <p class="mb-2">
        <?= Yii::t('app', 'Use for access control.') ?><br>
        <?= Yii::t('app', 'No guarantee.') . "\n" ?>
      </p>
      <p class="mb-2">
        <?= Html::a(
          Yii::t('app', 'Please read this notice when you want to automate the process.'),
          ['site/about', '#' => 'automation'],
        ) . "\n" ?>
      </p>
    </div>
    <nav>
      <div class="mb-2 d-grid">
        <?= Html::a(
          Yii::t('app', 'Plain Text'),
          ['region/plain', 'cc' => $region->id],
          [
            'class' => 'btn btn-primary',
            'type' => 'text/plain',
          ]
        ) . "\n" ?>
      </div>
      <div class="dropdown d-grid">
        <?= Html::tag(
          'button',
          Yii::t('app', 'Access-Control Templates'),
          [
            'class' => 'btn btn-primary dropdown-toggle',
            'type' => 'button',
            'id' => 'download-access-control',
            'data' => [
              'bs-toggle' => 'dropdown',
            ],
            'aria' => [
              'haspopup' => 'true',
              'expanded' => 'false',
            ],
          ]
        ) . "\n" ?>
        <div class="dropdown-menu shadow" aria-labelledby="download-access-control"><?= implode('', array_map(
          fn (DownloadTemplate $model): string => Html::a(
            Html::encode($model->name),
            ['region/plain',
              'cc' => $region->id,
              'template' => $model->key,
            ],
            [
              'class' => 'dropdown-item',
              'type' => 'text/plain',
            ]
          ),
          DownloadTemplate::find()
            ->andWhere(['can_use_in_url' => true])
            ->orderBy(['key' => SORT_ASC])
            ->all(),
        )) ?></div>
      </div>
    </nav>
  </div>
</aside>
