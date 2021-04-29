<?php

declare(strict_types=1);

use app\models\MergedCidr;
use app\models\Region;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var Region $region
 */

if (Yii::$app->request->isPjax) {
  return;
}

$accessControls = [
  'apache' => 'Apache (.htaccess)',
  'apache24' => 'Apache 2.4',
  'nginx' => 'Nginx',
  'nginx-geo' => 'Nginx (Geo)',
  'iptables' => 'iptables',
  'postfix' => 'Postfix',
];

?>
<aside class="card border-primary">
  <div class="card-header bg-primary text-white">
    Download
  </div>
  <div class="card-body">
    <div class="text-muted">
      <p class="mb-2">
        アクセス制御等にご利用ください。<br>
        自動化される場合は必ず中身があることを確認してから反映するように設定してください。<br>
        内容は保証しません。
      </p>
    </div>
    <nav class="mb-3">
      <div class="mb-2">
        <?= Html::a(
          Html::encode('プレインテキスト'),
          ['region/plain', 'cc' => $region->id],
          [
            'class' => 'btn btn-block btn-primary',
            'type' => 'text/plain',
          ]
        ) . "\n" ?>
      </div>
      <div class="dropdown mb-2">
        <?= Html::tag('button', Html::encode('アクセス制御用ひな型'), [
          'class' => 'btn btn-primary btn-block dropdown-toggle',
          'type' => 'button',
          'id' => 'download-access-control',
          'data' => [
            'toggle' => 'dropdown',
          ],
          'aria' => [
            'haspopup' => 'true',
            'expanded' => 'false',
          ],
        ]) . "\n" ?>
        <div class="dropdown-menu" aria-labelledby="download-access-control"><?= implode('', array_map(
          fn($type, $label) => Html::a(
            Html::encode($label),
            ['region/plain',
              'cc' => $region->id,
              'template' => $type,
            ],
            [
              'class' => 'dropdown-item',
              'type' => 'text/plain',
            ]
          ),
          array_keys($accessControls),
          array_values($accessControls),
        )) ?></div>
      </div>
    </nav>
    <div class="text-muted small">
      <p class="mb-0">
        自動アクセスについて：この一覧に自動アクセスすることは<strong>想定しています</strong>が、内容は1日あたり高々1回しか更新されません。また、割り振りされた直後に運用開始されるわけでもありませんから、<strong>10分ごとに最新版を取得するなどは全く必要ない</strong>はずです。また、可能であれば User-Agent に連絡先を記載してください。
      </p>
    </div>
  </div>
</aside>
