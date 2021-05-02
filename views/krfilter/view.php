<?php

declare(strict_types=1);

use app\assets\FlagIconCssAsset;
use app\models\Krfilter;
use app\widgets\SnsWidget;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

$this->title = 'krfilter / eufilter : ' . Yii::$app->name;

$accessControls = [
  'apache' => 'Apache (.htaccess)',
  'apache24' => 'Apache 2.4',
  'nginx' => 'Nginx',
  'nginx-geo' => 'Nginx (Geo)',
  'ipsecurity' => 'IIS/Azure (ipSecurity)',
  'iptables' => 'iptables',
  'postfix' => 'Postfix',
];

FlagIconCssAsset::register($this);

$this->registerCss('.card-body li{margin-bottom:1rem}');

?>
<main>
  <h1 class="mb-4">
    krfilter / eufilter
  </h1>
  <aside class="mb-0">
    <?= SnsWidget::widget() . "\n" ?>
  </aside>
  <hr>
  <?= $this->render('//layouts/ads/top') . "\n" ?>
  <div class="row">
    <div class="col-12 mb-4">
      <div class="card card-primary">
        <div class="card-header bg-primary text-white">
          What's This?
        </div>
        <div class="card-body">
          <p>
            複数の国や地域からのアクセスをまとめて遮断するためのIPアドレスの一覧です。
            （以前某所で公開されていた「krfilter」に倣って名前をつけていますが、今となっては特に意味のあるものではありません）
          </p>
          <p>
            対象各国からの接続をまとめて遮断するために使用することができますが、次のような点に充分注意をしてください。
          </p>
          <ul>
            <li>
              このリストの内容は無保証です。
              そのようなことは無いように作っているつもりですが、漏れがあるかも知れませんし、多すぎるかもしれません。
              また、常に最新の情報を出力しているとは限りません。
            </li>
            <li>
              対象国の選び方は独断と偏見です。深い意味はありません。
            </li>
            <li>
              単純にリストを使用すると、意外なところでメールが受信できなくなったりするかもしれません。
              充分に理解の上使用してください。
            </li>
            <li>
              このリストを使用したことによるいかなる損害にも関知しません。
            </li>
            <li>
              各リストへの自動化したアクセスを行っても構いませんが、あらかじめ<?= Html::a('こちらのページ', ['site/about', '#' => 'automation']) ?>をご確認ください。
            </li>
          </ul>
          <p class="mb-0">
            なお、各国を統合しない個別のリストについては、それぞれの国/地域のページを参照してください。
          </p>
        </div>
      </div>
    </div>
  </div>
  <div class="row align-items-stretch">
<?php $query = Krfilter::find()
  ->with('regions')
  ->orderBy(['name' => SORT_ASC])
?>
<?php foreach ($query->all() as $filter) { ?>
<?php $regions = $filter->regions ?>
<?php usort($regions, fn($a, $b) => strcmp($a->id, $b->id)) ?>
    <div class="col-12 col-sm-6 col-lg-4 col-xl-3 mb-4">
      <div class="card card-primary h-100">
        <div class="card-header bg-primary text-white">
          <?= Html::encode($filter->name) . "\n" ?>
        </div>
        <div class="card-body d-flex flex-column">
          <p class="mb-2 flex-grow-1">
            <?= implode(' ', array_map(
              fn($model) => implode('', [
                Html::tag(
                  'span',
                  '',
                  [
                    'class' => [
                      'flag-icon',
                      "flag-icon-{$model->id}",
                    ],
                    'title' => $model->name_ja,
                  ]
                ),
                Html::tag('span', Html::encode("({$model->name_ja}), "), ['class' => 'sr-only']),
              ]),
              $regions,
            )) ?>の統合リストです。
          </p>
          <div class="mb-2">
            <?= Html::a(
              Html::encode('プレインテキスト'),
              ['krfilter/plain', 'id' => $filter->id],
              [
                'class' => 'btn btn-block btn-primary',
                'type' => 'text/plain',
              ]
            ) . "\n" ?>
          </div>
          <div class="dropdown">
            <?= Html::tag('button', Html::encode('アクセス制御用ひな型'), [
              'class' => 'btn btn-primary btn-block dropdown-toggle',
              'type' => 'button',
              'id' => 'download-access-control-' . $filter->id,
              'data' => [
                'toggle' => 'dropdown',
              ],
              'aria' => [
                'haspopup' => 'true',
                'expanded' => 'false',
              ],
            ]) . "\n" ?>
            <?= Html::tag(
              'div',
              implode('', array_map(
                fn($type, $label) => Html::a(
                  Html::encode($label),
                  ['krfilter/plain',
                    'id' => $filter->id,
                    'template' => $type,
                  ],
                  [
                    'class' => 'dropdown-item',
                    'type' => 'text/plain',
                  ]
                ),
                array_keys($accessControls),
                array_values($accessControls),
              )),
              [
                'class' => 'dropdown-menu',
                'aria' => [
                  'labelledby' => 'download-access-control-' . $filter->id,
                ],
              ]
            ) . "\n" ?>
          </div>
        </div>
      </div>
    </div>
<?php } ?>
  </div>
</main>
