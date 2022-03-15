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

$this->title = Yii::t('app/about', 'About Us') . ' - ' . Yii::$app->name;

$this->registerCss(
    (new Scss())
        ->compileString('
            #automation {
                li {
                    margin-bottom: 1rem;
                }
            }
        ')
        ->getCss()
);

ApplicationLanguage::registerLink(Yii::$app, ['site/about']);

?>
<main>
  <h1 class="mb-4">
    <?= Yii::t('app/about', 'About Us') . "\n" ?>
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
          <?= Yii::t('app/about', 'About Us') . "\n" ?>
        </div>
        <div class="card-body">
          <p>
            <?= Yii::t('app/about', 'This site provides a list of IPv4 addresses allocated to each country or region.') . "\n" ?>
          </p>
          <p>
            <?= Yii::t('app/about', 'The information is automatically retrieved and updated daily from the Regional Internet Registry (RIR).') . "\n" ?>
          </p>
          <p>
            <?= Yii::t('app/about', 'The information we provide is unguaranteed.') . "\n" ?>
            <?= Yii::t('app/about', 'No responsibility is assumed for any problems that may arise, whether they are caused by Regional Internet Registry, our website, you, or any other communication channels.') . "\n" ?>
            <?= Yii::t('app/about', 'You should not use the information on this website if you need to guarantee its accuracy.') . "\n" ?>
          </p>
<?php if (Yii::$app->params['repository']) { ?>
          <p>
            <?= Html::a(
              Yii::t('app/about', 'If you find any problems, please contact us here.'),
              Yii::$app->params['repository'],
              [
                'target' => '_blank',
                'rel' => 'external noopener noreferrer',
              ]
            ) . "\n" ?>
          </p>
<?php } ?>
          <p>
            <?= Yii::t('app/about', 'The definition of "what is country" and "name of country" are highly political issues.') . "\n" ?>
            <?= Yii::t('app/about', 'We do not have any political agenda.') . "\n" ?>
            <?= Yii::t('app/about', 'As a rule, we do not accept opinions or requests regarding these political domains.') . "\n" ?>
            <?= Yii::t('app/about', 'This site uses the classification as presented by the RIR.') . "\n" ?>
            <?= Yii::t('app/about', 'The Japanese names commonly used in Japan or on the Ministry of Foreign Affairs website, and the English names on Wikipedia and other sites are used.') . "\n" ?>
          </p>
          <p class="mb-0">
            <?= Yii::t('app', 'National flags of the countries are displayed using {source}.', [
              'source' => Html::a(Html::encode('flag-icons'), 'https://flagicons.lipis.dev/', [
                'target' => 'blank',
                'rel' => 'noopener noreferrer',
              ]),
            ]) . "\n" ?>
            <?= Yii::t('app', 'You may not see the latest flag, or you may see the incorrect flag.') . "\n" ?>
          </p>
        </div>
      </div>
      <div class="card border-primary mb-4" id="automation">
        <div class="card-header bg-primary text-white">
          <?= Yii::t('app/about', 'About Automated-Access') . "\n" ?>
        </div>
        <div class="card-body">
          <p>
            <?= Yii::t('app/about', 'The text files that can be displayed or downloaded with "{plainText}" and "{template}" are intended for automated access.', [
              'plainText' => Yii::t('app', 'Plain Text'),
              'template' => Yii::t('app', 'Access-Control Templates'),
            ]) . "\n" ?>
            <?= Yii::t('app/about', 'There is no problem with automated access.') . "\n" ?>
          </p>
          <p>
            <?= Yii::t('app/about', 'However, please keep the following points in mind in order to make effective use of limited computing resources.') . "\n" ?>
          </p>
          <ul>
            <li>
              <?= Yii::t('app/about', 'We are using CloudFlare as a reverse proxy.') . "\n" ?>
              <?= Yii::t('app/about', 'Your access may be automatically denied by CloudFlare\'s control.') . "\n" ?>
            </li>
            <li>
              <strong>
                <?= Yii::t('app/about', 'The database of this site is updated only once a day as a rule.') . "\n" ?>
              </strong>
              <?= Yii::t('app/about', 'Please think carefully about whether the execution interval of the cron job is meaningful.') . "\n" ?>
            </li>
            <li>
              <strong>
                <?= Yii::t('app/about', 'There is a high download access rate around 0 minutes every hour.') . "\n" ?>
              </strong>
              <?= Yii::t('app/about', 'Please distribute the traffic appropriately.') . "\n" ?>
              <?= Yii::t('app/about', 'If too many requests are received at the same time, it may take a long time to respond or it may not be possible to respond.') . "\n" ?>
            </li>
            <li>
              <strong>
                <?= Yii::t('app/about', 'Please include your contact information in the User-Agent whenever possible when access.') . "\n" ?>
              </strong>
            </li>
            <li>
              <?= Yii::t('app/about', 'Please check the downloaded file to make sure that the contents are present and in the expected format.') . "\n" ?>
              <?= Yii::t('app/about', 'We have returned empty or half-empty data in the past.') . "\n" ?>
            </li>
          </ul>
          <p class="mb-0">
            <?= Yii::t('app/about', 'Thanks in advance for your cooperation.') . "\n" ?>
          </p>
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
