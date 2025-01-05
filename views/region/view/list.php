<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\helpers\TypeHelper;
use app\models\AllocationCidr;
use app\models\Region;
use app\models\RegionStat;
use yii\bootstrap5\LinkPager;
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
    <?= Yii::t('app', 'Allocation List') . "\n" ?>
  </div>
  <div class="card-body">
<?php if (!$isPjax) { ?>
<?php $stats = $region->regionStats[0] ?>
<?php assert($stats instanceof RegionStat) ?>
    <div class="text-muted">
      <p class="mb-2">
        <?= Yii::t('app', 'This is a list of IP addresses allocated to {country}.', [
          'country' => preg_match('/^ja\b/i', Yii::$app->language)
            ? $region->name_ja
            : $region->name_en,
        ]) . "\n" ?>
      </p>
      <p class="mb-2">
        <?= Yii::t('app', 'In most cases, "{mergedCidr}" is more useful than this list.', [
          'mergedCidr' => Yii::t('app', 'Merged CIDR'),
        ]) . "\n" ?>
      </p>
      <p class="mb-2">
        <?= Yii::t('app', 'If you want to use it access control, use "{download}."', [
          'download' => Yii::t('app', 'Download'),
        ]) . "\n" ?>
      </p>
      <p class="mb-2">
        <?= Yii::t('app', 'There are {total,number,integer} IP addresses allocated to {country}. This is {totalPct} of the total address space, and {nonReservedPct} excluding reserved space.', [
          'country' => preg_match('/^ja\b/i', Yii::$app->language)
            ? $region->name_ja
            : $region->name_en,
          'total' => $stats->total_address_count,
          'totalPct' => Yii::$app->formatter->asPercent($stats->total_address_count / (1 << 32), 5),
          'nonReservedPct' => Yii::$app->formatter->asPercent($stats->total_address_count / ((1 << 32) - 592715776), 5),
        ]) . "\n" ?>
      </p>
    </div>
    <hr>
<?php } ?>
    <div style="margin:0 -1rem -0.5rem">
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
            'key' => fn (AllocationCidr $model): string => vsprintf('list-%s', [
              TypeHelper::shouldBeString(preg_replace('/[^0-9]/', '-', $model->cidr)),
            ]),
          ]),
          'columns' => [
            [
              'label' => Yii::t('app', 'CIDR'),
              'attribute' => 'cidr',
              'contentOptions' => [
                'class' => 'text-center font-roboto',
              ],
            ],
            [
              'label' => Yii::t('app', 'IP Address'),
              'value' => function (AllocationCidr $model): string {
                $tmp = explode('/', $model->cidr);
                // phpcs:ignore SlevomatCodingStandard.PHP.UselessParentheses.UselessParentheses
                $mask = (0xffffffff << (32 - (int)$tmp[1])) & 0xffffffff;
                $count = (~$mask) & 0xffffffff;
                return vsprintf('%s - %s', [
                  $tmp[0],
                  long2ip(ip2long($tmp[0]) + $count),
                ]);
              },
              'contentOptions' => [
                'class' => 'text-center d-none d-md-table-cell font-roboto',
              ],
              'headerOptions' => [
                'class' => 'd-none d-md-table-cell',
              ],
            ],
            [
              'label' => Yii::t('app', 'Alloc Date'),
              'attribute' => 'block.date',
              'format' => 'shortDate',
              'contentOptions' => [
                'class' => 'text-center d-none d-md-table-cell font-roboto',
              ],
              'headerOptions' => [
                'class' => 'd-none d-md-table-cell',
              ],
            ],
            [
              'label' => Yii::t('app', 'Registry'),
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
