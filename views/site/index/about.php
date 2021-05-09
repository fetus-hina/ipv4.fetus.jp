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
    <p>
      <?= Html::a('詳しくはこちらをご覧ください。', ['site/about']) . "\n" ?>
    </p>
    <p>
      自動化されたアクセスについても、上記ページをご参照ください。
    </p>
    <p class="mb-0">
      ダウンロード可能な形式についての一応明文化された仕様については、<?= Html::a(
        'こちらをご確認ください。',
        ['site/schema']
      ) . "\n" ?>
    </p>
  </div>
</div>
