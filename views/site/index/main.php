<?php

declare(strict_types=1);

use app\assets\FlagIconCssAsset;
use app\models\RegionStat;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */

FlagIconCssAsset::register($this);
SortableTableAsset::register($this);

?>
<div class="card border-primary">
  <div class="card-header bg-primary text-white">
    国/地域別IPアドレス割振数一覧
  </div>
  <div class="card-body">
    <div class="text-muted small">
      <p class="mb-2">
        見出しをクリックすると並び替えが行えます。
      </p>
      <p class="mb-2">
        国/地域名をクリックするとIPアドレスの一覧を表示します。（リンク先ページでアクセス制御用のひな形を取得できます）
      </p>
    </div>
    <div style="margin:0 -1.25rem -0.75rem">
      <div class="table-responsive">
        <?= GridView::widget([
          'layout' => '{items}',
          'tableOptions' => [
            'class' => 'table table-striped table-borderless table-hover table-sm text-nowrap table-sortable mb-0',
            'style' => [
              'font-size' => '14px',
            ],
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
          ]),
          'columns' => [
            [
              'label' => '',
              'attribute' => 'region_id',
              'format' => fn($t) => Html::tag('span', '', ['class' => [
                'flag-icon',
                "flag-icon-{$t}",
              ]]),
              'contentOptions' => fn($model) => [
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
              'label' => 'CC',
              'attribute' => 'region_id',
              'format' => fn($t) => Html::tag('code', Html::encode((string)$t)),
              'contentOptions' => fn($model) => [
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
              'label' => '国/地域名',
              'format' => 'raw',
              'value' => fn(RegionStat $model) => Html::a(
                Html::encode(vsprintf('%s (%s)', [
                  $model->region->name_ja,
                  $model->region->name_en,
                ])),
                ['region/view', 'cc' => $model->region_id]
              ),
              'contentOptions' => fn($model) => [
                'class' => 'text-wrap',
                'data' => [
                  'sort-value' => $model->region->name_ja,
                ],
              ],
              'headerOptions' => [
                'data' => [
                  'sort' => 'string',
                ],
              ],
            ],
            [
              'label' => 'IPアドレス数 ' .
                '<span class="arrow"><span class="fa fa-angle-down"></span></span>',
              'encodeLabel' => false,
              'attribute' => 'total_address_count',
              'format' => 'integer',
              'contentOptions' => fn($model) => [
                'class' => 'text-right',
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
              'label' => '対全空間',
              'format' => ['percent', 5],
              'value' => fn(RegionStat $model) => $model->total_address_count / (1 << 32),
              'contentOptions' => fn($model) => [
                'class' => 'text-right d-none d-md-table-cell',
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
              'label' => '除予約領域',
              'format' => ['percent', 5],
              'value' => fn(RegionStat $model) => $model->total_address_count / ((1 << 32) - 592715776),
              'contentOptions' => fn($model) => [
                'class' => 'text-right d-none d-md-table-cell',
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
              'label' => '最終割振日',
              'attribute' => 'last_allocation_date',
              'format' => ['date', 'short'],
              'contentOptions' => fn($model) => [
                'class' => 'text-right d-none d-md-table-cell',
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
