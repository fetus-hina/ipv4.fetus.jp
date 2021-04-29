<?php

declare(strict_types=1);

use app\assets\FlagIconCssAsset;
use app\models\Region;
use app\widgets\SnsWidget;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\View;

/**
 * @var View $this
 * @var Region $region
 */

FlagIconCssAsset::register($this);

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
    Html::tag('span', '', ['class' => [
      'flag-icon',
      "flag-icon-{$region->id}",
    ]]),
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
    <div class="col-12 d-lg-none">
      <div class="mb-4">
        <?= $this->render('//region/view/download', ['region' => $region]) . "\n" ?>
      </div>
    </div>
    <div class="col-12 col-lg-8">
<?php if ($region->getKrfilters()->exists()) { ?>
      <aside class="mb-4">
        <div class="alert alert-info">
          頻繁にアクセス拒否リストに使用すると思われる国をまとめた一覧があります。<br>
          <?= Html::a(
            '詳しくはこちらをご覧ください。',
            ['krfilter/view'],
            ['class' => 'alert-link']
          ) . "\n" ?>
        </div>
      </aside>
<?php } ?>
      <div class="mb-4">
        <?= $this->render('//region/view/list', ['region' => $region]) . "\n" ?>
      </div>
    </div>
    <div class="col-12 col-lg-4">
      <div class="mb-4 d-none d-lg-block">
        <?= $this->render('//region/view/download', ['region' => $region]) . "\n" ?>
      </div>
      <?= $this->render('//layouts/ads/side') . "\n" ?>
      <div class="mb-4">
        <?= $this->render('//region/view/cidr', ['region' => $region]) . "\n" ?>
      </div>
    </div>
  </div>
</main>
