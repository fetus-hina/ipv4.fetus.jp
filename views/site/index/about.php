<?php

declare(strict_types=1);

use yii\helpers\Html;

?>
<div class="card">
  <div class="card-header bg-primary text-white">
    当サイトについて
  </div>
  <div class="card-body">
    <p>
      情報は無保証です。
      掲載されている情報を利用することで発生したいかなる損害等について当サイトは関知しません。
    </p>
    <p>
      情報は毎日地域インターネットレジストリから自動的に取得して更新されます。
    </p>
<?php if (Yii::$app->params['repository']) { ?>
    <p>
      <?= vsprintf('何か不具合が発生している場合は、%s。', [
        Html::a(
          (is_string(Yii::$app->params['repository']) && substr(Yii::$app->params['repository'], 0, 19) === 'https://github.com/')
            ? 'GitHubへご連絡ください'
            : 'こちらへご連絡ください',
          Yii::$app->params['repository'],
          [
            'target' => '_blank',
            'rel' => 'external noopener noreferrer',
          ]
        ),
      ]) . "\n" ?>
    </p>
<?php } ?>
    <p>
      「何をもって国とするか（別の国・地域とみなすか）」「各国の名称」などはきわめて政治的な問題です。
      表示内容について何ら政治的意図をもって記載していません。
      「A国はB国の一部であり統合して表示するべき」などの意見は受け付けません。
    </p>
  </div>
</div>
