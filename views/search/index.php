<?php

declare(strict_types=1);

use app\assets\FlagIconsAsset;
use app\models\SearchForm;
use app\models\SearchResult;
use app\widgets\SnsWidget;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/**
 * @var SearchForm $form
 * @var ?SearchResult $result
 * @var View $this
 */

FlagIconsAsset::register($this);

$this->title = '検索結果 : ' . Yii::$app->name;
?>
<main>
  <h1 class="mb-4">
    検索結果
<?php if (!$form->hasErrors()) { ?>
    <?= Html::tag(
      'small',
      Html::encode(sprintf('(%s)', (string)$form->normalizedIP)),
      ['class' => 'text-muted']
    ) . "\n" ?>
<?php } ?>
  </h1>
  <aside class="mb-0">
    <?= SnsWidget::widget() . "\n" ?>
  </aside>
  <hr> 
  <?= $this->render('//layouts/ads/top') . "\n" ?>
  <div class="row">
    <div class="order-0 order-lg-1 col-12 col-lg-4">
      <aside class="mb-4">
        <?= $this->render('//site/index/search', ['form' => $form]) . "\n" ?>
      </aside>
      <div class="d-none d-lg-block">
        <?= $this->render('//layouts/ads/side') . "\n" ?>
      </div>
    </div>
    <div class="order-1 order-lg-0 col-12 col-lg-8">
      <div class="mb-4">
        <div class="card border-primary">
          <div class="card-header bg-primary text-white">
            検索結果
          </div>
          <div class="card-body">
<?php if ($result) { ?>
            <?= DetailView::widget([
              'options' => [
                'class' => 'table table-striped table-hover detail-view mb-3',
              ],
              'model' => $result,
              'attributes' => [
                [
                  'label' => 'IPアドレス',
                  'value' => $form->normalizedIP,
                ],
                [
                  'label' => '国または地域',
                  'value' => vsprintf('%s %s %s', [
                    Html::tag('span', '', ['class' => [
                      'flag-icon',
                      "flag-icon-{$result->region->id}",
                    ]]),
                    Html::tag('code', Html::encode($result->region->id)),
                    Html::a(
                      Html::encode(vsprintf('%s (%s)', [
                        $result->region->name_ja,
                        $result->region->name_en,
                      ])),
                      ['region/view', 'cc' => $result->region->id]
                    ),
                  ]),
                  'format' => 'raw',
                ],
                [
                  'label' => '割り振りブロック (※1)',
                  'value' => function () use ($result): string {
                    $cidrs = $result->block->allocationCidrs;
                    usort($cidrs, fn ($a, $b) => strnatcasecmp($a->cidr, $b->cidr));

                    return implode('', [
                      vsprintf('「%s」から %s 個 (%s ～ %s)', [
                        Html::tag(
                          'code',
                          Html::encode($result->block->start_address),
                          ['class' => 'text-body']
                        ),
                        Yii::$app->formatter->asInteger($result->block->count),
                        Html::tag(
                          'code',
                          Html::encode($result->block->start_address),
                          ['class' => 'text-body']
                        ),
                        Html::tag(
                          'code',
                          (string)long2ip(ip2long($result->block->start_address) + $result->block->count - 1),
                          ['class' => 'text-body']
                        ),
                      ]),
                      Html::tag(
                        'textarea',
                        implode(
                          "\n",
                          array_map(
                            fn ($row) => $row->cidr,
                            $cidrs
                          )
                        ),
                        [
                          'class' => 'form-control font-monospace',
                          'readonly' => true,
                          'rows' => (string)count($cidrs),
                          'style' => [
                            'background-color' => 'var(--bs-body-bg)',
                            'color' => 'var(--bs-body-text)',
                          ],
                        ]
                      ),
                    ]);
                  },
                  'format' => 'raw',
                ],
                [
                  'label' => 'Merged CIDR (※2)',
                  'value' => Html::tag('input', '', [
                    'class' => 'form-control font-monospace',
                    'type' => 'text',
                    'value' => $result->mergedCidr->cidr,
                    'readonly' => true,
                    'style' => [
                      'background-color' => 'var(--bs-body-bg)',
                      'color' => 'var(--bs-body-text)',
                    ],
                  ]),
                  'format' => 'raw',
                ],
                [
                  'label' => '割り振り日',
                  'attribute' => 'block.date',
                  'format' => 'date',
                ],
                [
                  'label' => 'レジストリ',
                  'attribute' => 'block.registry.name',
                ],
              ],
            ]) . "\n" ?>
            <div class="text-muted small">
              <p>
                (※1) 割り振りは開始アドレスと個数で指定され、必ずしも256個などの「きりのいい数」となるとは限りません。
                そのため、ひとつのブロックが複数のCIDRとして表現される場合があります。
              </p>
              <p class="mb-0">
                (※2) Merged CIDR は、各国または地域一覧画面の「Merged CIDR」のどれに含まれるかを示しています。<br>
                この部分の表示は、「割り振りブロック」と等しいことも、より大きいことも、そのブロックの一部を示していることもあります。
              </p>
            </div>
<?php } else { ?>
            <p class="mb-0">
              該当するデータが見つかりませんでした。
            </p>
<?php } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
