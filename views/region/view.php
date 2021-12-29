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

$this->title = vsprintf('%s[%s]に割り振りされたIPアドレスの一覧 : %s', [
  $region->name_ja,
  $region->id,
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
  <h1><?= vsprintf('%s%s %s', [
    FlagIcon::widget(['cc' => $region->id]),
    Html::encode($region->name_ja),
    Html::tag(
      'small',
      Html::encode($region->name_en),
      ['class' => 'text-muted', 'lang' => 'en']
    ),
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
            頻繁にアクセス拒否リストに使用すると思われる国をまとめた一覧があります。
          </p>
          <?= Html::tag(
            'p',
            Html::a(
              '詳しくはこちらをご覧ください。',
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
            ご注意ください
          </div>
          <div class="card-body">
            <p>
              このリストは、欧州地域全体に割り振られたIPアドレスの一覧ではありません。
            </p>
            <p class="mb-0">
              欧州地域の大半のIPアドレスは、各国に割り振られています。
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
