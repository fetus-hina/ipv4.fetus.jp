<?php

declare(strict_types=1);

use ScssPhp\ScssPhp\Compiler as Scss;
use app\widgets\SnsWidget;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

$this->title = '出力ファイル仕様 - ' . Yii::$app->name;

$this->registerCss(
    (new Scss())
        ->compileString('
            .card-body {
                ul {
                    &:last-chid {
                        margin-bottom: 0;
                    }
                }

                li {
                    margin-bottom: 1rem;

                    &:last-child {
                        margin-bottom: 0;
                    }
                }
            }
        ')
        ->getCss()
);
?>
<main>
  <h1 class="mb-4">
    出力ファイル仕様
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
          このページについて
        </div>
        <div class="card-body">
          <p>
            このページでは、各ページにある「プレインテキスト」「アクセス制御用ひな形」からダウンロードできるデータファイルの形式を定義します。
          </p>
          <p class="mb-0">
            このファイルが無くても「見ればわかる」ように作ってありますが、明示しておくのが重要かと思いましたのでここに記録します。
          </p>
        </div>
      </div>
      <div class="card border-primary mb-4">
        <div class="card-header bg-primary text-white">
          総則
        </div>
        <div class="card-body">
          <p>
            それぞれのデータ形式で上書き規定されない限り、次の情報が全ての形式で適用されます。
          </p>
          <ul>
            <li>
              文字コードはUTF-8です。いわゆるBOMはありません。
            </li>
            <li>
              改行コードはCR+LFまたはLFのいずれかです。
            </li>
            <li>
              それぞれのレコードは改行で区切られ、1レコード1行で記載されます。
            </li>
          </ul>
          <p>
            アクセス制御（アクセス許可・拒否）が明確にあらかじめ出力されている形式の場合、次の内容が適用されます。
          </p>
          <ul>
            <li>
              出力している国・地域が「日本」である場合、デフォルトで「許可」設定が出力されます。
            </li>
            <li>
              出力している国・地域が「日本」以外の場合、デフォルトで「拒否」設定で出力されます。
            </li>
            <li>
              URLに「<code>?control=allow</code>」を付与すると、「許可」設定に変更できます。
            </li>
            <li>
              URLに「<code>?control=deny</code>」を付与すると、「拒否」設定に変更できます。
            </li>
          </ul>
        </div>
      </div>
      <div class="card border-primary mb-4">
        <div class="card-header bg-primary text-white">
          プレインテキスト
        </div>
        <div class="card-body">
          <ul>
            <li>
              「<code>#</code>」で始まる行は全てコメントです。<br>
              内容については規定されず、人間が読むことを前提にしています。機械処理時にはこの行を単純に無視します。<br>
              コメントが行の途中から始まることはありません。<br>
              データレコード同士の間にも挟まる可能性があります。
            </li>
            <li>
              任意の場所に空行が挟まります。<br>
              データレコード同士の間にも挟まる可能性があります。
            </li>
            <li>
              データレコードはCIDRがそのまま配置されます。
            </li>
          </ul>
        </div>
      </div>
      <div class="card border-primary mb-4">
        <div class="card-header bg-primary text-white">
          Apache (.htaccess)
        </div>
        <div class="card-body">
          <ul>
            <li>
              Apache 2.2 形式のデータが出力されます。
            </li>
            <li>
              「<code>#</code>」で始まる行は全てコメントです。<br>
              内容については規定されず、人間が読むことを前提にしています。機械処理時にはこの行を単純に無視します。<br>
              コメントが行の途中から始まることはありません。<br>
              データレコード同士の間にも挟まる可能性があります。
            </li>
            <li>
              任意の場所に空行が挟まります。<br>
              データレコード同士の間にも挟まる可能性があります。
            </li>
          </ul>
        </div>
      </div>
      <div class="card border-primary mb-4">
        <div class="card-header bg-primary text-white">
          Apache 2.4
        </div>
        <div class="card-body">
          <ul>
            <li>
              Apache 2.4 形式のデータが出力されます。
            </li>
            <li>
              「<code>#</code>」で始まる行は全てコメントです。<br>
              内容については規定されず、人間が読むことを前提にしています。機械処理時にはこの行を単純に無視します。<br>
              コメントが行の途中から始まることはありません。<br>
              データレコード同士の間にも挟まる可能性があります。
            </li>
            <li>
              任意の場所に空行が挟まります。<br>
              データレコード同士の間にも挟まる可能性があります。
            </li>
          </ul>
        </div>
      </div>
      <div class="card border-primary mb-4">
        <div class="card-header bg-primary text-white">
          CSV
        </div>
        <div class="card-body">
          <ul>
            <li>
              CSV(RFC 4180)形式のデータが出力されます。<br>
              正しく取り扱うには、RFC 4180に準拠したパーサーが必要です。
              <ul>
                <li>
                  改行コードはCR+LFです。
                  （※v2.4.20210510.051523以前はバグのためLFで出力されていました）
                </li>
                <li>
                  各カラムの値はダブルクォートで括られるかもしれないし、括られないかもしれません。
                </li>
                <li>
                  カラムにダブルクォートを含むかもしれません。<br>
                  含む場合は、RFC 4180に示される通り、例えば「<code>A"B</code>」という値は「<code>"A""B"</code>」と出力されます。
                </li>
                <li>
                  カラムに改行を含むかもしれません。
                </li>
              </ul>
            </li>
            <li>
              BOMの出力はありませんので、Microsoft Excelではコメント部の日本語が文字化けする可能性があります。
            </li>
            <li>
              「<code>#</code>」で始まる行は全てコメントです。<br>
              内容については規定されず、人間が読むことを前提にしています。機械処理時にはこの行を単純に無視します。<br>
              コメントが行の途中から始まることはありません。<br>
              データレコード同士の間にも挟まる可能性があります。
            </li>
            <li>
              任意の場所に空行が挟まります。<br>
              データレコード同士の間にも挟まる可能性があります。
            </li>
            <li>
              カラムは左から、
              <ul>
                <li>CIDR</li>
                <li>開始アドレス</li>
                <li>終了アドレス</li>
                <li>プレフィックス</li>
                <li>サブネットマスク</li>
              </ul>
              の順で並んでいます。
              将来拡張される可能性があります（拡張の際は右側に追加されます）。
              ちょうど5カラムであることを期待してはいけません。
            </li>
          </ul>
        </div>
      </div>
      <div class="card border-primary mb-4">
        <div class="card-header bg-primary text-white">
          ipset (firewalld)
        </div>
        <div class="card-body">
          <ul>
            <li>
              Firewalldの設定ファイルとして利用できるXML形式を出力します。<br>
              /etc/firewalld/ipsets/に設置して利用します。<br>
              ipsetコマンドまたはfirewall-cmdからの利用はできません。
            </li>
            <li>
              XML整形式として妥当なデータが出力されます。
            </li>
            <li>
              「<code>&lt;!--</code>」から「<code>--&gt;</code>」の間はコメントです。<br>
              行ベースのプロセッサ（例えばgrep）で簡単に除去できるよう、コメントの中身は「<code>#</code>」で始まるようになっています。
            </li>
          </ul>
        </div>
      </div>
      <div class="card border-primary mb-4">
        <div class="card-header bg-primary text-white">
          IIS/Azure (ipSecurity)
        </div>
        <div class="card-body">
          <ul>
            <li>
              IISの設定ファイルの一部として利用できるXMLを出力します。
            </li>
            <li>
              XML整形式として妥当なデータが出力されます。<br>
              ルート要素は<code>&lt;ipSecurity&gt;</code>になっています。実際の利用には加工が必要だと思われます。
            </li>
            <li>
              「<code>&lt;!--</code>」から「<code>--&gt;</code>」の間はコメントです。<br>
              行ベースのプロセッサ（例えばgrep）で簡単に除去できるよう、コメントの中身は「<code>#</code>」で始まるようになっています。
            </li>
          </ul>
        </div>
      </div>
      <div class="card border-primary mb-4">
        <div class="card-header bg-primary text-white">
          iptables
        </div>
        <div class="card-body">
          <ul>
            <li>
              iptables-restore等で利用する形式のデータが出力されます。
              /etc/sysconfig/iptables等のiptables設定ファイルを編集して利用することになるでしょう。
            </li>
            <li>
              「<code>#</code>」で始まる行は全てコメントです。<br>
              内容については規定されず、人間が読むことを前提にしています。機械処理時にはこの行を単純に無視します。<br>
              コメントが行の途中から始まることはありません。<br>
              データレコード同士の間にも挟まる可能性があります。
            </li>
            <li>
              任意の場所に空行が挟まります。<br>
              データレコード同士の間にも挟まる可能性があります。
            </li>
            <li>
              アクセス制御変更はできません。常に<code>-A RULE1 -s (CIDR) -j RULE2</code>の形式で出力されます。<br>
              <code>sed</code>等を使って目的の動作になるように置換することになるでしょう。
            </li>
          </ul>
        </div>
      </div>
      <div class="card border-primary mb-4">
        <div class="card-header bg-primary text-white">
          Nginx
        </div>
        <div class="card-body">
          <ul>
            <li>
              Nginxのアクセス制御構文を出力します。
              serverやlocationの設定として組み込むことになるでしょう。
            </li>
            <li>
              「<code>#</code>」で始まる行は全てコメントです。<br>
              内容については規定されず、人間が読むことを前提にしています。機械処理時にはこの行を単純に無視します。<br>
              コメントが行の途中から始まることはありません。<br>
              データレコード同士の間にも挟まる可能性があります。
            </li>
            <li>
              任意の場所に空行が挟まります。<br>
              データレコード同士の間にも挟まる可能性があります。
            </li>
          </ul>
        </div>
      </div>
      <div class="card border-primary mb-4">
        <div class="card-header bg-primary text-white">
          Nginx (Geo)
        </div>
        <div class="card-body">
          <ul>
            <li>
              Nginxの<a href="http://nginx.org/en/docs/http/ngx_http_geo_module.html" rel="external" target="_blank">ngx_http_geo_module</a>で利用できる形式を出力します。
            </li>
            <li>
              変数名は「<code>$ipv4_jp</code>」のように「<code>$ipv4_</code> + <var>CC</var>」になっています。<br>
              krfilter/eufilterでは<var>CC</var>部分は<code>krfilter_1</code>のようになります。
            </li>
            <li>
              「<code>#</code>」で始まる行は全てコメントです。<br>
              内容については規定されず、人間が読むことを前提にしています。機械処理時にはこの行を単純に無視します。<br>
              コメントが行の途中から始まることはありません。<br>
              データレコード同士の間にも挟まる可能性があります。
            </li>
            <li>
              任意の場所に空行が挟まります。<br>
              データレコード同士の間にも挟まる可能性があります。
            </li>
          </ul>
        </div>
      </div>
      <div class="card border-primary mb-4">
        <div class="card-header bg-primary text-white">
          Postfix
        </div>
        <div class="card-body">
          <ul>
            <li>
              Postfixの<code>check_client_access</code>などで利用できる形式を出力します。<br>
              <code>smtpd_client_restrictions = check_client_access cidr:/etc/postfix/kr.cidr</code>のように指定することになるでしょう。
            </li>
            <li>
              「<code>#</code>」で始まる行は全てコメントです。<br>
              内容については規定されず、人間が読むことを前提にしています。機械処理時にはこの行を単純に無視します。<br>
              コメントが行の途中から始まることはありません。<br>
              データレコード同士の間にも挟まる可能性があります。
            </li>
            <li>
              任意の場所に空行が挟まります。<br>
              データレコード同士の間にも挟まる可能性があります。
            </li>
          </ul>
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
