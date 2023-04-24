<?php

declare(strict_types=1);

use app\helpers\ApplicationLanguage;
use app\models\Region;
use app\models\RegionStat;
use app\widgets\EuIsNotUnifiedWarning;
use app\widgets\Krfilter;
use app\widgets\RegionHeading;
use app\widgets\Rufilter;
use app\widgets\SnsWidget;
use app\widgets\ads\SideAd;
use app\widgets\ads\SkyscraperAd;
use app\widgets\ads\SpRectAd;
use app\widgets\ads\TopAd;
use yii\web\Controller;
use yii\web\View;

/**
 * @var View $this
 * @var Region $region
 */

$this->title = vsprintf('%s : %s', [
    Yii::t('app', 'List of IP addresses allocated to {ri} {countryEn} [{cc}]', [
        'cc' => $region->id,
        'countryEn' => $region->name_en,
        'countryJa' => $region->name_ja,
        'ri' => Yii::$app->formatter->asRegionalIndicator($region->id),
    ]),
    Yii::$app->name,
]);

ApplicationLanguage::registerLink(Yii::$app, ['region/view', 'cc' => $region->id]);

$metas = [];
if (!Yii::$app->request->isPjax) {
    $stats = $region->regionStats[0];
    assert($stats instanceof RegionStat);

    $metas['description'] = implode(' ', [
        Yii::t('app', 'This is a list of IP addresses allocated to {country}.', [
            'country' => preg_match('/^ja\b/i', Yii::$app->language)
                ? $region->name_ja
                : $region->name_en,
        ]),
        Yii::t('app', 'There are {total,number,integer} IP addresses allocated to {country}. This is {totalPct} of the total address space, and {nonReservedPct} excluding reserved space.', [
            'country' => preg_match('/^ja\b/i', Yii::$app->language)
                ? $region->name_ja
                : $region->name_en,
            'total' => $stats->total_address_count,
            'totalPct' => Yii::$app->formatter->asPercent($stats->total_address_count / (1 << 32), 5),
            'nonReservedPct' => Yii::$app->formatter->asPercent($stats->total_address_count / ((1 << 32) - 592715776), 5),
        ]),
    ]);
}
foreach ($metas as $name => $value) {
    $this->registerMetaTag(['name' => $name, 'content' => $value]);
}

if (
    Yii::$app->request->isPjax &&
    $this->context instanceof Controller
) {
    $this->context->layout = 'pjax';
}

?>
<main>
  <?= RegionHeading::widget([
    'region' => $region,
    'options' => ['tag' => 'h1'],
]) . "\n" ?>
  <aside class="mb-0">
    <?= SnsWidget::widget() . "\n" ?>
  </aside>
  <hr>
  <?= TopAd::widget() . "\n" ?>
  <div class="row">
    <div class="col-12 col-lg-8 mb-4">
      <?= $this->render('//region/view/download', ['region' => $region]) . "\n" ?>
    </div>
    <div class="d-none d-lg-block col-4 mb-4">
      <?= SideAd::widget() . "\n" ?>
    </div>
    <?= SpRectAd::widget() . "\n" ?>
    <?= Krfilter::widget(['region' => $region]) . "\n" ?>
    <?= Rufilter::widget(['region' => $region]) . "\n" ?>
    <div class="col-12 col-lg-8">
      <?= EuIsNotUnifiedWarning::widget(['region' => $region]) . "\n" ?>
      <div class="mb-4">
        <?= $this->render('//region/view/list', ['region' => $region]) . "\n" ?>
      </div>
    </div>
    <div class="col-12 col-lg-4">
      <div class="mb-4">
        <?= $this->render('//region/view/cidr', ['region' => $region]) . "\n" ?>
      </div>
      <?= SkyscraperAd::widget() . "\n" ?>
    </div>
  </div>
</main>
