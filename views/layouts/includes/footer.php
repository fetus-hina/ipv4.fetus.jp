<?php

declare(strict_types=1);

use yii\helpers\Html;
use yii\web\View;

/** @var View $this */

?>
<footer class="bg-light border-top">
  <div class="container text-end py-3">
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
<?php $gitRevision = Yii::$app->params['gitRevision']; ?>
    <?= Html::tag(
      'div',
      implode(', ', array_map(
        fn($html) => Html::tag('span', $html, ['class' => 'text-nowrap']),
        array_filter([
          Html::a(
            Html::encode('ソースコード'),
            Yii::$app->params['repository'],
            [
              'target' => '_blank',
              'rel' => 'external noopener noreferrer',
            ]
          ),
          ($gitRevision && $gitRevision['version'])
            ? Html::encode($gitRevision['version'])
            : null,
          ($gitRevision && $gitRevision['short'])
            ? Html::encode($gitRevision['short'])
            : null,
        ])
      )),
      ['class' => 'small']
    ) . "\n" ?>
<?php } ?>
    <?= $this->render('//layouts/includes/footer/database-timestamp') . "\n" ?>
  </div>
</footer>
