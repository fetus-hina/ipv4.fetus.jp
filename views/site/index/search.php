<?php

declare(strict_types=1);

use app\assets\BootstrapIconsAsset;
use app\models\SearchForm;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var SearchForm $form
 * @var View $this
 */

BootstrapIconsAsset::register($this);

?>
<div class="card border-primary">
  <div class="card-header bg-primary text-white">
    <?= Html::encode(Yii::t('app/search', 'Search IP Address')) . "\n" ?>
  </div>
  <div class="card-body">
    <?php $af = ActiveForm::begin([
      'action' => ['search/index'],
      'method' => 'get',
    ]); echo "\n" ?>
      <div class="mb-2">
        <?= $af->field($form, 'query')
          ->label(false)
          ->textInput([
            'placeholder' => Yii::t('app/search', 'e.g., {exampleIP}', [
                'exampleIP' => '203.0.113.1',
            ]),
          ]) . "\n"
        ?>
      </div>
      <div class="d-grid">
        <?= Html::submitButton(
          implode(' ', [
            Html::tag('span', '', ['class' => 'bi bi-search']),
            Html::encode(Yii::t('app/search', 'Search')),
          ]),
          ['class' => 'btn btn-primary']
        ) . "\n" ?>
      </div>
    <?php ActiveForm::end(); echo "\n" ?>
  </div>
</div>
