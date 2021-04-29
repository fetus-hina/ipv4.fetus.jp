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
    Merged CIDR
  </div>
  <div class="card-body">
    <div class="text-muted">
      <p class="mb-2">
        連続したブロックをまとめたものです。
      </p>
    </div>
    <div style="margin:0 -1.25rem -0.75rem">
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
