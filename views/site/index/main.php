<?php

declare(strict_types=1);

use app\models\RegionStat;
use app\widgets\AllocationSummaryChart;
use app\widgets\FlagIcon;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */

SortableTableAsset::register($this);

$rowNumber = 0;

?>
<div class="card border-primary">
  <div class="card-header bg-primary text-white">
    <?= Yii::t('app', 'IPv4 Address Allocation List for Each Country/Region') . "\n" ?>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-4 col-md-7 col-lg-8 mb-2">
        <div class="text-muted">
          <p class="mb-2">
            <?= Yii::t('app', 'Click on a heading to sort.') . "\n" ?>
          </p>
          <p class="m-0">
            <?= Yii::t('app', 'Click on the country/region name to display a list of IP addresses.') . "\n" ?>
            <?= Yii::t('app', '(You can get a template for access control from the linked page)') . "\n" ?>
          </p>
        </div>
      </div>
      <div class="col-8 col-md-5 col-lg-4 order-md-2 mb-2">
        <?= AllocationSummaryChart::widget() . "\n" ?>
      </div>
    </div>
    <div style="margin:0 -1rem -0.5rem">
      <div class="table-responsive">
        <?= GridView::widget([
          'layout' => '{items}',
          'tableOptions' => [
            'class' => 'table table-striped table-borderless table-hover table-sm text-nowrap table-sortable mb-0',
          ],
          'headerRowOptions' => [
            'style' => [
              'cursor' => 'pointer',
            ],
          ],
          'dataProvider' => Yii::createObject([
            'class' => ActiveDataProvider::class,
            'query' => RegionStat::find()
              ->with(['region'])
              ->orderBy([
                'total_address_count' => SORT_DESC,
                'last_allocation_date' => SORT_DESC,
              ]),
            'sort' => false,
            'pagination' => false,
            'key' => 'region_id',
          ]),
          'columns' => [
            [
              'label' => '',
              'attribute' => 'region_id',
              'format' => function (string $t) use (&$rowNumber): string {
                return FlagIcon::widget([
                  'cc' => $t,
                  'useImg' => (++$rowNumber <= 30),
                ]);
              },
              'contentOptions' => fn (RegionStat $model): array => [
                'class' => 'text-center',
                'data' => [
                  'sort-value' => $model->region_id,
                ],
              ],
              'headerOptions' => [
                'data' => [
                  'sort' => 'string',
                ],
              ],
            ],
            [
              'label' => Yii::t('app/cclist', 'CC'),
              'attribute' => 'region_id',
              'format' => fn (string $t): string => Html::tag('code', Html::encode((string)$t)),
              'contentOptions' => fn (RegionStat $model): array => [
                'class' => 'text-center',
                'data' => [
                  'sort-value' => $model->region_id,
                ],
              ],
              'headerOptions' => [
                'data' => [
                  'sort' => 'string',
                ],
              ],
            ],
            [
              'label' => Yii::t('app/cclist', 'Country/Region'),
              'format' => 'raw',
              'value' => fn (RegionStat $model): string => Html::a(
                $model->region?->formattedHtmlName ?? '',
                ['region/view', 'cc' => $model->region_id]
              ),
              'contentOptions' => fn (RegionStat $model): array => [
                'class' => 'text-wrap',
                'data' => [
                  'sort-value' => $model->region?->formattedName ?? '',
                ],
              ],
              'headerOptions' => [
                'data' => [
                  'sort' => 'string',
                ],
              ],
            ],
            [
              'label' => Yii::t('app/cclist', 'IP Addresses') .
                ' <span class="arrow"><span class="bi bi-arrow-down-short"></span></span>',
              'encodeLabel' => false,
              'attribute' => 'total_address_count',
              'format' => 'integer',
              'contentOptions' => fn (RegionStat $model): array => [
                'class' => 'text-end font-roboto',
                'data' => [
                  'sort-value' => $model->total_address_count,
                ],
              ],
              'headerOptions' => [
                'data' => [
                  'sort' => 'int',
                ],
              ],
            ],
            [
              'label' => Yii::t('app/cclist', 'Alloc %'),
              'format' => ['percent', 5],
              'value' => fn (RegionStat $model): int|float => $model->total_address_count / (1 << 32),
              'contentOptions' => fn (RegionStat $model): array => [
                'class' => 'text-end d-none d-md-table-cell font-roboto',
                'data' => [
                  'sort-value' => $model->total_address_count,
                ],
              ],
              'headerOptions' => [
                'class' => 'd-none d-md-table-cell',
                'data' => [
                  'sort' => 'int',
                ],
              ],
            ],
            [
              'label' => Yii::t('app/cclist', 'Non Reserved'),
              'format' => ['percent', 5],
              'value' => fn (RegionStat $model): int|float => $model->total_address_count / ((1 << 32) - 592715776),
              'contentOptions' => fn (RegionStat $model): array => [
                'class' => 'text-end d-none d-md-table-cell font-roboto',
                'data' => [
                  'sort-value' => $model->total_address_count,
                ],
              ],
              'headerOptions' => [
                'class' => 'd-none d-md-table-cell',
                'data' => [
                  'sort' => 'int',
                ],
              ],
            ],
            [
              'label' => Yii::t('app/cclist', 'Last Alloc'),
              'attribute' => 'last_allocation_date',
              'format' => 'shortDate',
              'contentOptions' => fn (RegionStat $model): array => [
                'class' => 'text-end d-none d-md-table-cell font-roboto',
                'data' => [
                  'sort-value' => $model->last_allocation_date ?? '',
                ],
              ],
              'headerOptions' => [
                'class' => 'd-none d-md-table-cell',
                'data' => [
                  'sort' => 'string',
                ],
              ],
            ],
          ],
        ]) . "\n" ?>
      </div>
    </div>
  </div>
</div>
