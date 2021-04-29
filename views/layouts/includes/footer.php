<?php

declare(strict_types=1);

use yii\helpers\Html;
use yii\web\View;

/** @var View $this */

?>
<footer class="bg-light border-top">
  <div class="container text-right py-3">
    <div>
      <?= implode(' ', array_filter([
        vsprintf('Copyright © %s %s.', [
          Html::encode(Yii::$app->params['copyrightYear'] ?? ''),
          Html::a(
            Html::encode(Yii::$app->params['copyrightHolder'] ?? 'the Author'),
            Yii::$app->params['authorWebsite'] ?? ['site/index']
          ),
        ]),
        (Yii::$app->params['authorTwitter'] ?? null)
          ? Html::a(
            Html::tag('span', '', ['class' => 'fab fa-twitter']),
            sprintf('https://twitter.com/%s', Yii::$app->params['authorTwitter']),
            [
              'target' => '_blank',
              'title' => sprintf('Twitter @%s', Yii::$app->params['authorTwitter']),
              'rel' => 'external noopener noreferrer',
            ]
          )
          : '',
        (Yii::$app->params['authorGitHub'] ?? null)
          ? Html::a(
            Html::tag('span', '', ['class' => 'fab fa-github']),
            sprintf('https://github.com/%s', Yii::$app->params['authorGitHub']),
            [
              'target' => '_blank',
              'title' => sprintf('GitHub @%s', Yii::$app->params['authorGitHub']),
              'rel' => 'external noopener noreferrer',
            ]
          )
          : '',
      ])) . "\n" ?>
    </div>
<?php if (Yii::$app->params['repository'] ?? '') { ?>
    <div class="small">
      <?= Html::a('ソースコード', Yii::$app->params['repository'], [
        'target' => '_blank',
        'rel' => 'external noopener noreferrer',
      ]) . "\n" ?>
    </div>
<?php } ?>
  </div>
</footer>
