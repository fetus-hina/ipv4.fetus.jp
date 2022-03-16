<?php

declare(strict_types=1);

use app\assets\AppAsset;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var string $content
 */

AppAsset::register($this);

$this->registerCsrfMetaTags();

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
  <head>
    <?= Html::tag('meta', '', ['charset' => Yii::$app->charset]) . "\n" ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head(); echo "\n" ?>
  </head>
  <body>
<?php $this->beginBody() ?>
    <?= $content ?><?= "\n" ?>
<?php $this->endBody() ?>
  </body>
</html>
<?php $this->endPage() ?>
