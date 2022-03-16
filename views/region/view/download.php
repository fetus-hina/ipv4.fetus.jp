<?php

declare(strict_types=1);

use app\models\DownloadTemplate;
use app\models\MergedCidr;
use app\models\Region;
use app\widgets\DownloadButtons;
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
    <?= DownloadButtons::widget([
        'downloadLinkCreator' => fn (?DownloadTemplate $template) => [
            'region/plain',
            'cc' => $region->id,
            'template' => $template?->key,
        ],
    ]) . "\n" ?>
  </div>
</aside>
