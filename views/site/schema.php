<?php

declare(strict_types=1);

use ScssPhp\ScssPhp\Compiler as Scss;
use app\helpers\ApplicationLanguage;
use app\widgets\SnsWidget;
use app\widgets\ads\SideAd;
use app\widgets\ads\SkyscraperAd;
use app\widgets\ads\TopAd;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

$this->title = implode(' - ', [
    Yii::t('app/schema', 'Output Specifications'),
    Yii::$app->name,
]);

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

ApplicationLanguage::registerLink(Yii::$app, ['site/schema']);

?>
<main>
  <h1 class="mb-4">
    <?= Yii::t('app/schema', 'Output Specifications') . "\n" ?>
  </h1>
  <aside class="mb-0">
    <?= SnsWidget::widget() . "\n" ?>
  </aside>
  <hr>
  <?= TopAd::widget() . "\n" ?>
  <div class="row">
    <div class="col-12 col-lg-8">
      <div class="card border-primary mb-4">
        <div class="card-header bg-primary text-white">
          <?= Yii::t('app/schema', 'About This') . "\n" ?>
        </div>
        <div class="card-body">
          <p class="mb-0">
            <?= Yii::t('app/schema', 'This page defines the format of data files that can be downloaded from "{plainText}" and "{template}" on each page.', [
              'plainText' => Yii::t('app', 'Plain Text'),
              'template' => Yii::t('app', 'Access-Control Templates'),
            ]) . "\n" ?>
          </p>
        </div>
      </div>
      <div class="card border-primary mb-4">
        <div class="card-header bg-primary text-white">
          <?= Yii::t('app/schema', 'General Rules') . "\n" ?>
        </div>
        <div class="card-body">
          <p>
            <?= Yii::t('app/schema', 'Unless overridden by the individual data formats, the following information applies to all formats.') . "\n" ?>
          </p>
          <ul>
            <li>
              <?= Yii::t('app/schema', 'The encoding is UTF-8. No BOM (Byte Order Mark). It may contains out-of-ASCII characters such as Japanese text.') . "\n" ?>
            </li>
            <li>
              <?= Yii::t('app/schema', 'The new-line code is one of CR+LF or LF.') . "\n" ?>
            </li>
            <li>
              <?= Yii::t('app/schema', 'Each record is separated by a new-line.') . "\n" ?>
            </li>
          </ul>
          <p>
            <?= Yii::t('app/schema', 'In case of a format where access control is pre-output, the following will be applied.') . "\n" ?>
          </p>
          <ul>
            <li>
              <?= Yii::t('app/schema', 'If the country/region being output is "Japan", the "Allow" setting is output by default.') . "\n" ?>
            </li>
            <li>
              <?= Yii::t('app/schema', 'If the country/region being output is not "Japan", the "Deny" setting is output by default.') . "\n" ?>
            </li>
            <li>
              <?= Yii::t('app/schema', 'You can change the setting to "Allow" by adding "{code}" to the URL.', [
                'code' => '<code>?control=allow</code>',
              ]) . "\n" ?>
            </li>
            <li>
              <?= Yii::t('app/schema', 'You can change the setting to "Deny" by adding "{code}" to the URL.', [
                'code' => '<code>?control=deny</code>',
              ]) . "\n" ?>
            </li>
          </ul>
        </div>
      </div>
      <div class="card border-primary mb-4">
        <div class="card-header bg-primary text-white">
          <?= Yii::t('app', 'Plain Text') . "\n" ?>
        </div>
        <div class="card-body">
          <ul>
            <li>
              <?= $this->render('//site/schema/hash-comment') . "\n" ?>
            </li>
            <li>
              <?= $this->render('//site/schema/blank-line') . "\n" ?>
            </li>
            <li>
              <?= Yii::t('app/schema', 'CIDRs are output as is.') . "\n" ?>
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
              <?= Yii::t('app/schema', 'Apache 2.2 format data is output.') . "\n" ?>
            </li>
            <li>
              <?= $this->render('//site/schema/hash-comment') . "\n" ?>
            </li>
            <li>
              <?= $this->render('//site/schema/blank-line') . "\n" ?>
            </li>
          </ul>
        </div>
      </div>
      <div class="card border-primary mb-4">
        <div class="card-header bg-primary text-white">
          Apache (.htaccess), packed
        </div>
        <div class="card-body">
          <ul>
            <li>
              <?= Yii::t('app/schema', 'Apache 2.2 format data is output.') . "\n" ?>
            </li>
            <li>
              <?= Yii::t('app/schema', 'Pack multiple CIDRs into a single line.') . "\n" ?>
            </li>
            <li>
              <?= $this->render('//site/schema/hash-comment') . "\n" ?>
            </li>
            <li>
              <?= $this->render('//site/schema/blank-line') . "\n" ?>
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
              <?= Yii::t('app/schema', 'Apache 2.4 format data is output.') . "\n" ?>
            </li>
            <li>
              <?= $this->render('//site/schema/hash-comment') . "\n" ?>
            </li>
            <li>
              <?= $this->render('//site/schema/blank-line') . "\n" ?>
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
              <?= Yii::t('app/schema', 'CSV (RFC 4180) format data is output.') ?><br>
              <?= Yii::t('app/schema', 'To handle it correctly, a parser that conforms to RFC 4180 is required.') . "\n" ?>
              <ul>
                <li>
                  <?= Yii::t('app/schema', 'The new-line code is CR+LF.') . "\n" ?>
                </li>
                <li>
                  <?= Yii::t('app/schema', 'The value of each column may or may not be enclosed in double quotes.') . "\n" ?>
                </li>
                <li>
                  <?= Yii::t('app/schema', 'The column may contain double quotes.') ?><br>
                  <?= Yii::t('app/schema', 'If it contains double-quotes, for example, the value "{code1}" will be printed as "{code2}", as shown in RFC 4180.', [
                    'code1' => '<code>A"B</code>',
                    'code2' => '<code>"A""B"</code>',
                  ]) . "\n" ?>
                </li>
                <li>
                  <?= Yii::t('app/schema', 'The column may contain line breaks.') . "\n" ?>
                </li>
              </ul>
            </li>
            <li>
              <?= Yii::t('app/schema', 'Because no BOM is output, it is possible that non-ASCII characters in the comment part will be {mojibake} in Microsoft Excel.', [
                'mojibake' => Html::a(
                  '<i>mojibake</i>',
                  'https://en.wikipedia.org/wiki/Mojibake',
                  [
                    'rel' => 'nofollow noopener',
                    'target' => '_blank',
                  ],
                ),
              ]) . "\n" ?>
            </li>
            <li>
              <?= $this->render('//site/schema/hash-comment') . "\n" ?>
            </li>
            <li>
              <?= $this->render('//site/schema/blank-line') . "\n" ?>
            </li>
            <li>
              <?= Yii::t('app/schema', 'The columns are output in the following order.') . "\n" ?>
              <ul>
                <li>
                  <?= Yii::t('app/schema', 'CIDR') . "\n" ?>
                </li>
                <li>
                  <?= Yii::t('app/schema', 'Start Address') . "\n" ?>
                </li>
                <li>
                  <?= Yii::t('app/schema', 'End Address') . "\n" ?>
                </li>
                <li>
                  <?= Yii::t('app/schema', 'Prefix') . "\n" ?>
                </li>
                <li>
                  <?= Yii::t('app/schema', 'Subnet Mask') . "\n" ?>
                </li>
              </ul>
              <?= Yii::t('app/schema', 'It may be expanded in the future (when expanded, it will be added to the right side).') ?><br>
              <?= Yii::t('app/schema', 'Do not expect it is exactly {num,number,integer} columns.', [
                'num' => 5,
              ]) . "\n" ?>
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
              <?= Yii::t('app', 'Outputs XML format can be used as Firewalld configuration file.') ?><br>
              <?= Yii::t('app', 'It is installed into {path} for use.', [
                'path' => '<code>/etc/firewalld/ipsets/</code>',
              ]) ?><br>
              <?= Yii::t('app', 'You cannot use it from the {ipset} or {firewallcmd} command.', [
                'ipset' => '<code>ipset</code>',
                'firewallcmd' => '<code>firewall-cmd</code>',
              ]) . "\n" ?>
            </li>
            <li>
              <?= Yii::t('app', 'The output is well-formed XML.') . "\n" ?>
            </li>
            <li>
              <?= $this->render('//site/schema/xml-comment') . "\n" ?>
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
              <?= Yii::t('app', 'Outputs XML format can be used as part of IIS configuration file.') . "\n" ?>
            </li>
            <li>
              <?= Yii::t('app', 'The output is well-formed XML.') . "\n" ?>
            </li>
            <li>
              <?= implode('<br>', [
                Yii::t('app', 'The root element is {element}.', [
                  'element' => '<code>' . Html::encode('<ipSecurity>') . '</code>',
                ]),
                Yii::t('app', 'You need to edit the XML with an XML processor to use it.'),
              ]) . "\n" ?>
            </li>
            <li>
              <?= $this->render('//site/schema/xml-comment') . "\n" ?>
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
              <?= Yii::t('app', 'The output is in data format for use with {command}, etc.', [
                'command' => '<code>iptables-restore</code>',
              ]) ?><br>
              <?= Yii::t('app', 'Used as part of a configuration file for {command}, such as {path}.', [
                'command' => '<code>iptables</code>',
                'path' => '<code>/etc/sysconfig/iptables</code>',
              ]) ?>
            </li>
            <li>
              <?= $this->render('//site/schema/hash-comment') . "\n" ?>
            </li>
            <li>
              <?= $this->render('//site/schema/blank-line') . "\n" ?>
            </li>
            <li>
              <?= implode('<br>', [
                Yii::t('app', 'You cannot change the access control.'),
                Yii::t('app', 'The output is always in the format {format}.', [
                  'format' => '<code>-A RULE1 -s 198.51.100.0/24 -j RULE2</code>',
                ]),
                Yii::t('app', 'You will need to use {command} etc. to replace it to get the expected behavior.', [
                  'command' => '<code>sed</code>',
                ]),
              ]) ?>
            </li>
          </ul>
        </div>
      </div>
      <div class="card border-primary mb-4">
        <div class="card-header bg-primary text-white">
          ipv4bycc compat.
        </div>
        <div class="card-body">
          <ul>
            <li>
              <?= Yii::t(
                'app',
                'The output is in the format compatible with <a href="{url}" target="_blank" rel="nofollow noopener">this web site</a>.',
                [
                  'url' => 'http://nami.jp/ipv4bycc/',
                ],
              ) . "\n" ?>
            </li>
            <li>
              <?= Yii::t(
                'app',
                'However, the output includes comment lines and it is output separately for each country/region.',
              ) . "\n" ?>
            </li>
            <li>
              <?= $this->render('//site/schema/hash-comment') . "\n" ?>
            </li>
            <li>
              <?= $this->render('//site/schema/blank-line') . "\n" ?>
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
              <?= implode('<br>', [
                Yii::t('app', 'Outputs the access control syntax for Nginx.'),
                Yii::t('app', 'You will probably include it as a {server} or {location} setting.', [
                  'server' => '<code>server</code>',
                  'location' => '<code>location</code>',
                ]),
              ]) . "\n" ?>
            </li>
            <li>
              <?= $this->render('//site/schema/hash-comment') . "\n" ?>
            </li>
            <li>
              <?= $this->render('//site/schema/blank-line') . "\n" ?>
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
              <?= Yii::t('app', 'Output the format used by Nginx\'s {module}.', [
                'module' => Html::a(
                  'nginx_http_geo_module',
                  'http://nginx.org/en/docs/http/ngx_http_geo_module.html',
                  [
                    'rel' => 'external noopener noreferrer',
                    'target' => '_blank',
                  ],
                ),
              ]) . "\n" ?>
            </li>
            <li>
              <?= Yii::t('app', 'The variable name is like "{example}", which is "{define}."', [
                'example' => '<code>$ipv4_jp</code>',
                'define' => '<code>$ipv4_</code>+<var>CC</var>',
              ]) ?><br>
              <?= Yii::t('app', 'For krfilter/eufilter, it will be like "{example}."', [
                'example' => '<code>$ipv4_krfilter_1</code>',
              ]) . "\n" ?>
            </li>
            <li>
              <?= $this->render('//site/schema/hash-comment') . "\n" ?>
            </li>
            <li>
              <?= $this->render('//site/schema/blank-line') . "\n" ?>
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
              <?= Yii::t('app', 'Outputs formats which can be used for Postfix\'s {var}, etc.', [
                'var' => '<code>check_client_access</code>',
              ]) ?><br>
              <?= Yii::t('app', 'Set up and use it like "{example}".', [
                'example' => '<code>smtpd_client_restrictions = check_client_access cidr:/etc/postfix/kr.cidr</code>',
              ]) . "\n" ?>
            </li>
            <li>
              <?= $this->render('//site/schema/hash-comment') . "\n" ?>
            </li>
            <li>
              <?= $this->render('//site/schema/blank-line') . "\n" ?>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="col-12 col-lg-4">
      <div class="mb-4">
        <?= SideAd::widget() . "\n" ?>
      </div>
      <div>
        <?= SkyscraperAd::widget() . "\n" ?>
      </div>
    </div>
  </div>
</main>
