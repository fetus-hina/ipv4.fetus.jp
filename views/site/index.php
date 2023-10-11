<?php

declare(strict_types=1);

use app\helpers\ApplicationLanguage;
use app\models\SearchForm;
use app\widgets\AboutUsCard;
use app\widgets\GitAccessCard;
use app\widgets\Ipv4byccCard;
use app\widgets\NginxGeoCard;
use app\widgets\SearchCard;
use app\widgets\SnsWidget;
use app\widgets\StandWithUkraineCard;
use app\widgets\ads\SideAd;
use app\widgets\ads\SkyscraperAd;
use app\widgets\ads\SpRectAd;
use app\widgets\ads\TopAd;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var SearchForm $search
 * @var View $this
 */

$this->title = Yii::t('app', '{siteName}: IPv4 Address Allocation List for Each Country/Region', [
    'siteName' => Yii::$app->name,
]);

$metas = [
    'description' => strip_tags(Yii::t('app', 'IPv4 Address Allocation List for Each Country/Region')),
    'keywords' => implode(',', [
        'IPv4',
        'IP Address',
        'IPアドレス',
        'IPv4 Address',
        'IPv4 Allocation',
        'IPv4 Assign',
        'IPv4アドレス',
        'IPv4割り振り',
        'IPv4割り当て',
        '国別IP',
    ]),
];
foreach ($metas as $name => $value) {
    $this->registerMetaTag(['name' => $name, 'content' => $value]);
}

ApplicationLanguage::registerLink(Yii::$app, ['site/index']);

?>
<main>
  <?= Html::tag(
      'h1',
      Yii::t('app', 'IPv4 Address Allocation List for Each Country/Region'),
      ['class' => 'mb-4'],
  ) . "\n" ?>
  <?= Html::tag('aside', SnsWidget::widget(), ['class' => 'mb-0']) . "\n" ?>
  <hr>
  <?= TopAd::widget() . "\n" ?>
  <div class="row">
    <?= Html::tag(
        'div',
        Html::tag(
            'aside',
            SearchCard::widget(['form' => $search]),
            ['class' => 'mb-4'],
        ),
        ['class' => 'col-12 d-lg-none'],
    ) . "\n" ?>
    <?= SpRectAd::widget() . "\n" ?>
    <div class="col-12 col-lg-8">
      <div class="mb-4">
        <?= $this->render('//site/index/main') . "\n" ?>
      </div>
    </div>
    <?= Html::tag(
        'div',
        implode('', [
            StandWithUkraineCard::widget(),
            Html::tag('aside', SearchCard::widget(['form' => $search]), ['class' => 'mb-4 d-none d-lg-block']),
            SideAd::widget(),
            Html::tag('aside', AboutUsCard::widget(), ['class' => 'mb-4']),
            Html::tag('aside', GitAccessCard::widget(), ['class' => 'mb-4']),
            Html::tag('aside', Ipv4byccCard::widget(), ['class' => 'mb-4']),
            Html::tag('aside', NginxGeoCard::widget(), ['class' => 'mb-4']),
            SkyscraperAd::widget(),
        ]),
        [
            'class' => [
                'col-12',
                'col-lg-4',
            ],
        ],
    ) . "\n" ?>
  </div>
</main>
