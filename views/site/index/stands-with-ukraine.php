<?php

declare(strict_types=1);

use app\widgets\StandsWithUkraine;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

?>
<aside class="mb-4">
  <div class="card border-primary">
    <div class="card-body">
      <?= StandsWithUkraine::widget([
        'marginClass' => 'mb-0',
      ]) . "\n" ?>
    </div>
  </div>
</aside>
