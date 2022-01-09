<?php

declare(strict_types=1);

use yii\helpers\Html;

?>
<div class="card">
  <div class="card-header bg-primary text-white">
    <?= Yii::t('app/about', 'About Us') . "\n" ?>
  </div>
  <div class="card-body">
    <p>
      <?= Yii::t('app/about', 'Information is not guaranteed.') . "\n" ?>
      <?= Yii::t('app/about', 'We are not responsible for any damage caused by the use of the information on this site.') . "\n" ?>
    </p>
    <p>
      <?= Yii::t('app/about', 'The information is updated automatically every day by retrieving it from the Regional Internet Registry.') . "\n" ?>
    </p>
    <p>
      <?= Html::a(
        Yii::t('app/about', 'Click here for more information.'),
        ['site/about']
      ) . "\n" ?>
    </p>
    <p>
      <?= Yii::t('app/about', 'For automated access, see the page above.') . "\n" ?>
    </p>
    <p class="mb-0">
      <?= Html::a(
        Yii::t('app/about', 'For specifications about downloadable formats, please click here.'),
        ['site/schema'],
      ) . "\n" ?>
    </p>
  </div>
</div>
