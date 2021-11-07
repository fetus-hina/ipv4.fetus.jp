<?php

declare(strict_types=1);

use app\models\SearchForm;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/**
 * @var SearchForm $form
 */

?>
<div class="card border-primary">
  <div class="card-header bg-primary text-white">
    IPアドレス検索
  </div>
  <div class="card-body">
    <?php $af = ActiveForm::begin([
      'action' => ['search/index'],
      'method' => 'get',
    ]); echo "\n" ?>
      <div class="mb-2">
        <?= $af->field($form, 'query')
          ->label(false)
          ->textInput(['placeholder' => '例: 203.0.113.1']) . "\n"
        ?>
      </div>
      <div class="d-grid">
        <?= Html::submitButton(
          Html::tag('span', '', ['class' => 'fas fa-search']) . ' 検索',
          ['class' => 'btn btn-primary']
        ) . "\n" ?>
      </div>
    <?php ActiveForm::end(); echo "\n" ?>
  </div>
</div>
