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
    Download
  </div>
  <div class="card-body">
    <div class="text-muted">
      <p class="mb-2">
        アクセス制御等にご利用ください。<br>
        自動化される際は<?= Html::a('こちらの注意事項', ['site/about', '#' => 'automation']) ?>をご確認ください。<br>
        内容は保証しません。
      </p>
    </div>
    <nav>
      <div class="mb-2 d-grid">
        <?= Html::a(
          Html::encode('プレインテキスト'),
          ['region/plain', 'cc' => $region->id],
          [
            'class' => 'btn btn-primary',
            'type' => 'text/plain',
          ]
        ) . "\n" ?>
      </div>
      <div class="dropdown d-grid">
        <?= Html::tag('button', Html::encode('アクセス制御用ひな型'), [
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
        ]) . "\n" ?>
        <div class="dropdown-menu shadow" aria-labelledby="download-access-control"><?= implode('', array_map(
          fn ($model) => Html::a(
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
