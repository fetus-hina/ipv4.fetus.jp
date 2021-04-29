<?php

declare(strict_types=1);

use app\models\AllocationCidr;
use app\models\Region;
use app\models\RegionStat;
use yii\bootstrap4\LinkPager;
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

$isPjax = (bool)Yii::$app->request->isPjax;
if ($isPjax) {
  if (Yii::$app->request->get('_pjax') !== '#pjax-cidr-list') {
    return;
  }
}

?>
<div class="card border-primary" id="cidr-list">
  <div class="card-header bg-primary text-white">
    割り振り一覧
  </div>
  <div class="card-body">
<?php if (!$isPjax) { ?>
<?php $stats = $region->regionStats[0] ?>
<?php assert($stats instanceof RegionStat) ?>
    <div class="text-muted">
      <p class="mb-2">
        <?= Html::encode($region->name_ja) ?>に割り振りられたIPアドレスの一覧です。
      </p>
      <p class="mb-2">
        データをアクセス制御に使いたい方は「Download」をご利用ください。
      </p>
      <p class="mb-2">
        大体のケースでは「Merged CIDR」の表示のほうが便利です。（「だれが」「いつ」の情報は必要ないため）
      </p>
      <p class="mb-2">
        <?= Html::encode(
          vsprintf('%sには%s個のIPアドレスが割り振られています。これは全アドレス空間の%s、予約領域を除くと%sです。', [
            $region->name_ja,
            Yii::$app->formatter->asInteger($stats->total_address_count),
            Yii::$app->formatter->asPercent($stats->total_address_count / (1 << 32), 5),
            Yii::$app->formatter->asPercent($stats->total_address_count / ((1 << 32) - 592715776), 5),
          ])
        ) . "\n" ?>
      </p>
    </div>
    <hr>
<?php } ?>
    <div style="margin:0 -1.25rem -0.75rem">
      <div class="table-responsive">
<?php Pjax::begin([
  'id' => 'pjax-cidr-list',
  'scrollTo' => new JsExpression('jQuery("#cidr-list").offset().top'),
]) ?>
        <?= GridView::widget([
          'dataProvider' => Yii::createObject([
            'class' => ActiveDataProvider::class,
            'query' => AllocationCidr::find()
              ->innerJoinWith(['block'], true)
              ->with('block.registry')
              ->andWhere(['{{%allocation_block}}.[[region_id]]' => $region->id])
              ->orderBy(['cidr' => SORT_ASC]),
            'sort' => false,
            'pagination' => [
              'pageParam' => 'list-page',
              'pageSizeParam' => 'list-per-page',
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
            [
              'label' => 'IPアドレス',
              'value' => function (AllocationCidr $model): string {
                $tmp = explode('/', $model->cidr);
                $mask = (0xffffffff << (32 - (int)$tmp[1])) & 0xffffffff;
                $count = (~$mask) & 0xffffffff;
                return vsprintf('%s - %s', [
                  $tmp[0],
                  long2ip(ip2long($tmp[0]) + $count),
                ]);
              },
              'contentOptions' => [
                'class' => 'text-center d-none d-md-table-cell',
              ],
              'headerOptions' => [
                'class' => 'd-none d-md-table-cell',
              ],
            ],
            [
              'label' => '割り振り日',
              'attribute' => 'block.date',
              'format' => ['date', 'short'],
              'contentOptions' => [
                'class' => 'text-center d-none d-md-table-cell',
              ],
              'headerOptions' => [
                'class' => 'd-none d-md-table-cell',
              ],
            ],
            [
              'label' => 'レジストリ',
              'attribute' => 'block.registry.name',
              'contentOptions' => [
                'class' => 'text-center d-none d-md-table-cell',
              ],
              'headerOptions' => [
                'class' => 'd-none d-md-table-cell',
              ],
            ],
          ],
        ]) . "\n" ?>
<?php Pjax::end() ?>
      </div>
    </div>
  </div>
</div>
