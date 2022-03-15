<?php

declare(strict_types=1);

use app\helpers\ApplicationLanguage;
use app\helpers\TypeHelper;
use app\models\AllocationCidr;
use app\models\SearchForm;
use app\models\SearchResult;
use app\widgets\FlagIcon;
use app\widgets\SearchCard;
use app\widgets\SnsWidget;
use app\widgets\ads\SideAd;
use app\widgets\ads\TopAd;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/**
 * @var SearchForm $form
 * @var ?SearchResult $result
 * @var View $this
 */

$this->title = Yii::t('app/search', 'Search Results') . ' : ' . Yii::$app->name;

ApplicationLanguage::registerLink(Yii::$app, ['search/index', 'query' => $form->normalizedIP]);

?>
<main>
  <h1 class="mb-4">
    <?= Html::encode(Yii::t('app/search', 'Search Results')) . "\n" ?>
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
  <?= TopAd::widget() . "\n" ?>
  <div class="row">
    <div class="order-0 order-lg-1 col-12 col-lg-4">
      <aside class="mb-4">
        <?= SearchCard::widget(['form' => $form]) . "\n" ?>
      </aside>
      <div class="d-none d-lg-block">
        <?= SideAd::widget() . "\n" ?>
      </div>
    </div>
    <div class="order-1 order-lg-0 col-12 col-lg-8">
      <div class="mb-4">
        <div class="card border-primary">
          <div class="card-header bg-primary text-white">
            <?= Html::encode(Yii::t('app/search', 'Search Results')) . "\n" ?>
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
                  'label' => Yii::t('app/search', 'IP Address'),
                  'value' => $form->normalizedIP,
                ],
                [
                  'label' => Yii::t('app/search', 'Country/Region'),
                  'value' => vsprintf('%s %s %s', [
                    FlagIcon::widget(['cc' => $result->region->id]),
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
                  'label' => Yii::t('app/search', 'Allocation Block') . ' ' . Yii::t('app', '(*1)'),
                  'value' => function () use ($result): string {
                    $cidrs = $result->block->allocationCidrs;
                    usort(
                      $cidrs,
                      fn (AllocationCidr $a, AllocationCidr $b): int => strnatcasecmp($a->cidr, $b->cidr),
                    );

                    return implode('', [
                      Yii::t(
                        'app/search',
                        '{count,number,integer} addresses from "{startAddress}" ({startAddress} - {endAddress})',
                        [
                          'count' => (int)$result->block->count,
                          'startAddress' => Html::tag(
                            'code',
                            Html::encode($result->block->start_address),
                            ['class' => 'text-body'],
                          ),
                          'endAddress' => Html::tag(
                            'code',
                            TypeHelper::shouldBeString(
                              long2ip(
                                TypeHelper::shouldBeInteger(ip2long($result->block->start_address))
                                  + $result->block->count
                                  - 1
                              ),
                            ),
                            ['class' => 'text-body'],
                          ),
                        ]
                      ),
                      Html::tag(
                        'textarea',
                        implode(
                          "\n",
                          array_map(
                            fn (AllocationCidr $row): string => $row->cidr,
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
                  'label' => Yii::t('app/search', 'Merged CIDR') . ' ' . Yii::t('app', '(*2)'),
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
                  'label' => Yii::t('app/search', 'Allocated Date'),
                  'attribute' => 'block.date',
                  'format' => 'longDate',
                ],
                [
                  'label' => Yii::t('app/search', 'Registry'),
                  'attribute' => 'block.registry.name',
                ],
              ],
            ]) . "\n" ?>
            <div class="text-muted small">
              <p>
                <?= Yii::t('app', '(*1)') . "\n" ?>
                <?= implode('<br>', [
                  Yii::t('app/search', 'The allocation is specified by the starting address and the number of addresses, which may not always be a "good number" such as 256.'),
                  Yii::t('app/search', 'For this reason, a single allocation may be expressed as multiple CIDRs.'),
                ]) . "\n" ?>
              </p>
              <p class="mb-0">
                <?= Yii::t('app', '(*2)') . "\n" ?>
                <?= implode('<br>', [
                  Yii::t('app/search', 'The {mergedCidrThis} indicates which of the "{mergedCidrForeign}" in the Country/Region List is included.', [
                    'mergedCidrThis' => Yii::t('app/search', 'Merged CIDR'),
                    'mergedCidrForeign' => Yii::t('app', 'Merged CIDR'),
                  ]),
                  Yii::t('app/search', 'This part of the display may be equal to, greater than, or part of the "Allocation Block".'),
                ]) . "\n" ?>
              </p>
            </div>
<?php } else { ?>
            <p class="mb-0">
              <?= Yii::t('app/search', 'No data was found.') . "\n" ?>
            </p>
<?php } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
