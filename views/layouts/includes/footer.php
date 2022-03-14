<?php

declare(strict_types=1);

use app\assets\BootstrapIconsAsset;
use app\widgets\Tooltip;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

BootstrapIconsAsset::register($this);

?>
<footer>
  <div class="container">
    <div>
      <?= implode(' ', array_filter([
        vsprintf('Copyright © %s %s.', [
          Html::encode(Yii::$app->params['copyrightYear'] ?? ''),
          Html::a(
            Html::encode(Yii::$app->params['copyrightHolder'] ?? 'the Author'),
            Yii::$app->params['authorWebsite'] ?? ['site/index']
          ),
        ]),
        Yii::$app->params['authorTwitter'] ?? null
          ? Html::a(
            Tooltip::widget([
              'content' => Html::tag('span', '', ['class' => 'bi bi-twitter']),
              'format' => 'raw',
              'title' => sprintf('Twitter @%s', Yii::$app->params['authorTwitter']),
            ]),
            sprintf('https://twitter.com/%s', Yii::$app->params['authorTwitter']),
            [
              'target' => '_blank',
              'rel' => 'external noopener noreferrer',
            ]
          )
          : '',
        Yii::$app->params['authorGitHub'] ?? null
          ? Html::a(
            Tooltip::widget([
              'content' => Html::tag('span', '', ['class' => 'bi bi-github']),
              'format' => 'raw',
              'title' => sprintf('GitHub @%s', Yii::$app->params['authorGitHub']),
            ]),
            sprintf('https://github.com/%s', Yii::$app->params['authorGitHub']),
            [
              'target' => '_blank',
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
        fn (string $html): string => Html::tag('span', $html, ['class' => 'text-nowrap']),
        array_filter([
          Html::a(
            Yii::t('app', 'Source Code'),
            Yii::$app->params['repository'],
            [
              'target' => '_blank',
              'rel' => 'external noopener noreferrer',
            ]
          ),
          $gitRevision && $gitRevision['version']
            ? Html::encode($gitRevision['version'])
            : null,
          $gitRevision && $gitRevision['short']
            ? Html::encode($gitRevision['short'])
            : null,
        ])
      )),
      ['class' => 'small']
    ) . "\n" ?>
<?php } ?>
    <div class="small">
      <?= Html::a(
        Yii::t('app', 'Open Source Licenses'),
        ['license/index']
      ) . "\n" ?>
    </div>
    <?= $this->render('//layouts/includes/footer/database-timestamp') . "\n" ?>
    <?= $this->render('//layouts/includes/footer/flag-icons') . "\n" ?>
  </div>
</footer>
