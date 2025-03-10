<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\AppAsset;
use app\helpers\FaviconHelper;
use app\widgets\ContentWrapper;
use app\widgets\FetusHeader;
use app\widgets\Footer;
use app\widgets\Navbar;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var string $content
 */

AppAsset::register($this);

FaviconHelper::registerLinkTags($this);
$this->registerCsrfMetaTags();
$this->registerMetaTag([
    'name' => 'viewport',
    'content' => 'width=device-width,initial-scale=1,shrink-to-fit=no',
]);
$this->registerMetaTag([
    'http-equiv' => 'X-UA-Compatible',
    'content' => 'IE=edge',
]);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<?= Html::beginTag('html', [
    'class' => 'h-100',
    'lang' => Yii::$app->language,
]) . "\n" ?>
  <head>
    <?= Html::tag('meta', '', ['charset' => Yii::$app->charset]) . "\n" ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head(); echo "\n" ?>
  </head>
  <?= Html::beginTag('body', [
    'class' => [
        'back-to-top-auto',
        'h-100',
    ],
]) . "\n" ?>
<?php $this->beginBody() ?>
    <?= Html::tag(
        'div',
        implode('', [
            FetusHeader::widget(),
            Html::tag('div', Navbar::widget(), ['class' => 'mb-4']),
            ContentWrapper::widget(['content' => $content]),
            Html::tag('div', Footer::widget()),
        ]),
        [
            'class' => [
                'd-flex',
                'flex-column',
                'h-100',
                'justify-content-start',
            ],
        ],
    ) . "\n" ?>
<?php $this->endBody() ?>
  </body>
</html>
<?php $this->endPage() ?>
