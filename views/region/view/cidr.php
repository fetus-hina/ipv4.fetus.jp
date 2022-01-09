<?php

declare(strict_types=1);

use app\models\MergedCidr;
use app\models\Region;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var View $this
 * @var Region $region
 */

if (
  Yii::$app->request->isPjax &&
  Yii::$app->request->get('_pjax') !== '#pjax-merged-cidr'
) {
  return;
}

?>
<div class="card border-primary" id="merged-cidr">
  <div class="card-header bg-primary text-white">
    <?= Yii::t('app', 'Merged CIDR') . "\n" ?>
  </div>
  <div class="card-body">
    <div class="text-muted">
      <p class="mb-2">
        <?= Yii::t('app', 'This is a consolidated list of contiguous allocation blocks.') . "\n" ?>
      </p>
    </div>
    <div style="margin:0 -1rem -0.5rem">
      <div class="table-responsive">
<?php Pjax::begin([
  'id' => 'pjax-merged-cidr',
  'scrollTo' => new JsExpression('jQuery("#merged-cidr").offset().top'),
]) ?>
        <?= GridView::widget([
          'dataProvider' => Yii::createObject([
            'class' => ActiveDataProvider::class,
            'query' => $region->getMergedCidrs()
              ->orderBy(['cidr' => SORT_ASC]),
            'sort' => false,
            'pagination' => [
              'pageParam' => 'cidr-page',
              'pageSizeParam' => 'cidr-per-page',
            ],
            'key' => fn ($model) => 'cidr-' . preg_replace('/[^0-9]/', '-', $model->cidr),
          ]),
          'columns' => [
            [
              'label' => 'CIDR',
              'attribute' => 'cidr',
              'contentOptions' => [
                'class' => 'text-center',
              ],
            ],
          ],
        ]) . "\n" ?>
<?php Pjax::end() ?>
      </div>
    </div>
  </div>
</div>
