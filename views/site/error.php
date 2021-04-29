<?php

declare(strict_types=1);

use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var string $name
 * @var string $message
 * @var Exception $exception
 */

$this->title = Yii::$app->name . ' : ' . $name;

?>
<main>
  <h1><?= Html::encode($name) ?></h1>
  <p><?= nl2br(Html::encode($message)) ?></p>
</main>
