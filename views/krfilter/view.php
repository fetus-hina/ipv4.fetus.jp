<?php

declare(strict_types=1);

use ScssPhp\ScssPhp\Compiler as Scss;
use app\helpers\ApplicationLanguage;
use app\models\DownloadTemplate;
use app\models\Krfilter;
use app\models\Region;
use app\widgets\DownloadButtons;
use app\widgets\FlagIcon;
use app\widgets\KrfilterCard;
use app\widgets\KrfilterTargetListWidget;
use app\widgets\SnsWidget;
use app\widgets\Tooltip;
use app\widgets\ads\TopAd;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 */

$this->title = 'krfilter / eufilter : ' . Yii::$app->name;

$this->registerCss(
    (new Scss())
        ->compileString('
            .card-body {
                .note-list {
                    li {
                        margin-bottom: 1rem;
                    }
                }
            }
        ')
        ->getCss()
);

ApplicationLanguage::registerLink(Yii::$app, ['krfilter/view']);

$krfilters = Krfilter::find()
  ->with(['regions'])
  ->orderBy(['name' => SORT_ASC])
  ->all();

?>
<main>
  <h1 class="mb-4">
    krfilter / eufilter
  </h1>
  <aside class="mb-0">
    <?= SnsWidget::widget() . "\n" ?>
  </aside>
  <hr>
  <?= TopAd::widget() . "\n" ?>
  <div class="row">
    <div class="col-12 mb-4">
      <div class="card card-primary">
        <div class="card-header bg-primary text-white">
          <?= Yii::t('app/krfilter', 'About This') . "\n" ?>
        </div>
        <div class="card-body">
          <p>
            <?= Yii::t('app/krfilter', 'This is a list of IP addresses to block access from multiple countries or regions at once.') ?><br>
            <?= Yii::t('app/krfilter', 'The name "krfilter" is a historical term and does not have any meaning at this time.') . "\n" ?>
          </p>
          <p>
            <?= Yii::t('app/krfilter', 'This can be used to block all connections from the target countries at once, but please be very careful about the following points.') . "\n" ?>
          </p>
          <ul class="note-list">
            <li>
              <?= implode('<br>', [
                Yii::t('app/krfilter', 'The contents of this list are not guaranteed.'),
                Yii::t('app/krfilter', 'Some IP addresses may be missing, or there may be too many.'),
                Yii::t('app/krfilter', 'Also, it may not always output the latest information.'),
              ]) . "\n" ?>
            </li>
            <li>
              <?= Yii::t('app/krfilter', 'We are not responsible for any damage caused by the use of this list.') . "\n" ?>
            </li>
            <li>
              <?= Yii::t('app/krfilter', 'The selection of target countries is based on my own judgment and prejudice. There is no strong meaning.') . "\n" ?>
            </li>
            <li>
              <?= Yii::t('app/krfilter', 'If you use this list to reject/discard emails, you might encounter unexpected behavior.') ?><br>
              <?= Yii::t('app/krfilter', 'Please use this list with full understanding.') . "\n" ?>
            </li>
            <li>
              <?= Yii::t('app/krfilter', 'You can automate access to each list, but {link}please check this page beforehand.</a>', [
                'link' => '<a href="' . Url::to(['site/about', '#' => 'automation']) . '">',
              ]) . "\n" ?>
            </li>
          </ul>
          <p class="mb-0">
            <?= Yii::t('app/krfilter', 'For individual lists that do not consolidate countries, please refer to the respective country/region page.') . "\n" ?>
          </p>
        </div>
      </div>
    </div>
  </div>
  <?= Html::tag(
    'div',
    implode('', array_map(
      fn (Krfilter $model): string => KrfilterCard::widget(['model' => $model]),
      $krfilters,
    )),
    ['class' => 'row align-items-stretch'],
  ) . "\n" ?>
</main>
