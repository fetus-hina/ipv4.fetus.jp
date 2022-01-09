<?php

declare(strict_types=1);

use app\models\Region;
use app\widgets\FlagIcon;
use app\widgets\SnsWidget;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\View;

/**
 * @var View $this
 * @var Region $region
 */

$this->title = vsprintf('%s : %s', [
  Yii::t('app', 'List of IP addresses allocated to {countryEn} [{cc}]', [
    'cc' => $region->id,
    'countryEn' => $region->name_en,
    'countryJa' => $region->name_ja,
  ]),
  Yii::$app->name,
]);

if (
  Yii::$app->request->isPjax &&
  $this->context instanceof Controller
) {
  $this->context->layout = 'pjax';
}

?>
<main>
  <h1><?= vsprintf('%s%s', [
    FlagIcon::widget(['cc' => $region->id]),
    preg_match('/^en\b/i', Yii::$app->language)
      ? Html::encode($region->name_en)
      : vsprintf('%s %s', [
        Html::encode($region->name_ja),
        Html::tag(
          'small',
          Html::encode($region->name_en),
          [
            'class' => 'text-muted',
            'lang' => 'en',
          ],
        ),
      ]),
  ]) ?></h1>
  <aside class="mb-0">
    <?= SnsWidget::widget() . "\n" ?>
  </aside>
  <hr>
  <?= $this->render('//layouts/ads/top') . "\n" ?>
  <div class="row">
    <div class="col-12 col-lg-8 mb-4">
      <?= $this->render('//region/view/download', ['region' => $region]) . "\n" ?>
    </div>
    <div class="d-none d-lg-block col-4 mb-4">
      <?= $this->render('//layouts/ads/side') . "\n" ?>
    </div>
    <?= $this->render('//layouts/ads/sp-rect') . "\n" ?>
<?php if ($region->getKrfilters()->exists()) { ?>
    <aside class="col-12 mb-4">
      <div class="card border-info">
        <div class="card-header bg-info text-white">
          krfilter / eufilter
        </div>
        <div class="card-body">
          <p>
            <?= Yii::t('app', 'We have a list of countries that we think are frequently used for denied access lists.') . "\n" ?>
          </p>
          <?= Html::tag(
            'p',
            Html::a(
              Yii::t('app', 'For more information, please click here.'),
              ['krfilter/view']
            ),
            ['class' => 'mb-0']
          ) . "\n" ?>
        </div>
      </div>
    </aside>
<?php } ?>
    <div class="col-12 col-lg-8">
<?php if ($region->id === 'eu') { ?>
      <aside class="mb-4">
        <div class="card border-danger">
          <div class="card-header bg-danger text-white">
            <?= Yii::t('app', 'Attention') . "\n" ?>
          </div>
          <div class="card-body">
            <p>
              <?= Yii::t('app', 'This list is NOT a complete list of IP addresses allocated to the entire European region.') . "\n" ?>
            </p>
            <p>
              <?= Yii::t('app', 'Most IP addresses in the European region are allocated to individual countries.') . "\n" ?>
            </p>
            <p class="mb-0">
              <?= Yii::t('app', 'Refer to {eufilter} if you need the whole integrated list.', [
                'eufilter' => Html::a(
                  'eufilter',
                  ['krfilter/view'],
                ),
              ]) . "\n" ?>
            </p>
          </div>
        </div>
      </aside>
<?php } ?>
      <div class="mb-4">
        <?= $this->render('//region/view/list', ['region' => $region]) . "\n" ?>
      </div>
    </div>
    <div class="col-12 col-lg-4">
      <div class="mb-4">
        <?= $this->render('//region/view/cidr', ['region' => $region]) . "\n" ?>
      </div>
      <?= $this->render('//layouts/ads/side-skyscraper') . "\n" ?>
    </div>
  </div>
</main>
