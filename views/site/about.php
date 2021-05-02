<?php

declare(strict_types=1);

use app\widgets\SnsWidget;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

$this->title = 'About - ' . Yii::$app->name;

?>
<main>
  <h1 class="mb-4">
    このサイトについて
  </h1>
  <aside class="mb-0">
    <?= SnsWidget::widget() . "\n" ?>
  </aside>
  <hr>
  <?= $this->render('//layouts/ads/top') . "\n" ?>
  <div class="row">
    <div class="col-12 col-lg-8">
      <div class="card border-primary mb-4">
        <div class="card-header bg-primary text-white">
          このサイトについて
        </div>
        <div class="card-body">
          <p>
            このサイトは、各「国や地域」に割り振りされたIPv4アドレスの一覧を提供するサイトです。
          </p>
          <p>
            情報は、<a href="https://ja.wikipedia.org/wiki/%E5%9C%B0%E5%9F%9F%E3%82%A4%E3%83%B3%E3%82%BF%E3%83%BC%E3%83%8D%E3%83%83%E3%83%88%E3%83%AC%E3%82%B8%E3%82%B9%E3%83%88%E3%83%AA">地域インターネットレジストリ(<abbr title="Regional Internet Registry">RIR</abbr>)</a>から毎日自動的に取得し、更新されます。
          </p>
          <p>
            提供する情報は無保証です。地域インターネットレジストリ側、当サイト側、利用者側、その他通信経路等のいずれかで発生した問題かにかかわらず、一切の責任を負いません。正確性等の保証が欲しい場合はご自分で頑張るか、（他者の提供する<small class="text-muted">（かもしれない）</small>）保証ありのサービスをご利用ください。
          </p>
<?php if (Yii::$app->params['repository']) { ?>
          <p>
            <?= vsprintf('何か不具合が発生している場合は、%s。', [
              Html::a(
                is_string(Yii::$app->params['repository']) &&
                substr(Yii::$app->params['repository'], 0, 19) === 'https://github.com/'
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
          <p class="mb-0">
            「何をもって国とするか（別の国・地域とみなすか）」「各国の名称」などはきわめて政治的な問題です。表示内容について何ら政治的意図をもって記載していません。「A国はB国の一部であり統合して表示するべき」などの意見は受け付けません。このサイトではRIRで提示されている分類をそのまま利用し、日本で一般的な、または外務省サイトに掲載されている日本語名称、Wikipedia等に掲載されている英語名称を利用しています。
          </p>
        </div>
      </div>
      <div class="card border-primary mb-4" id="automation">
        <div class="card-header bg-primary text-white">
          自動化されたアクセスについて
        </div>
        <div class="card-body">
          <p>
            「プレインテキスト」「アクセス制御用ひな型」で表示またはダウンロードできるテキストファイルは、自動化アクセスを想定しています。自動化されたアクセスをしても何ら問題ありません。
          </p>
          <p>
            ただし、限られた計算リソースを有効活用するため、次の点にご留意ください。
          </p>
          <ul>
            <li>
              リバースプロキシとしてCloudFlareが挟まっています。CloudFlare側の制御で自動的にアクセスが拒否される場合があります。
            </li>
            <li>
              このサイトのデータベースは原則として<strong>毎日1回だけ更新されます</strong>。<strong>cron等の実行間隔に意味があるのか</strong>はよくお考え下さい。
            </li>
            <li>
              <strong>毎時0分1～3秒頃はダウンロードアクセスが集中しています</strong>。適当に分散をお願いします。PHPプロセスの同時実行数に制限をかけていますから最悪レスポンスが返せなくなります。きちんとデータを受け取りたいならずらしたほうが良いでしょう。
            </li>
            <li>
              自動化アクセス時には、可能な限り<strong>User-Agentに連絡先を記載してください</strong>。「curlやwgetであることを通知してくる」よりよほど役に立ちます。例えば、上述の毎時0分にアクセスしてくる方にお願いの連絡を差し上げることができるかどうかは運営としては大きいです。
            </li>
            <li>
              ダウンロードしたファイルに中身がきちんと存在するか、想定したフォーマットであるかは必ず確認してください。過去に空のデータを返したことも、データが半減していたこともあります。
            </li>
          </ul>
          <p class="mb-0">
            ご協力をお願いします。
          </p>
<?php $this->registerCss('#automation li{margin-bottom:1rem') ?>
        </div>
      </div>
    </div>
    <div class="col-12 col-lg-4">
      <div class="mb-4">
        <?= $this->render('//layouts/ads/side') . "\n" ?>
      </div>
      <div>
        <?= $this->render('//layouts/ads/side-skyscraper') . "\n" ?>
      </div>
    </div>
  </div>
</main>
