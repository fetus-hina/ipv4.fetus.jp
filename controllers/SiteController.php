<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\helpers\ApplicationLanguage;
use app\models\SearchForm;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\ErrorAction;
use yii\web\Response;

use function function_exists;
use function implode;
use function is_string;
use function opcache_reset;
use function strtotime;

class SiteController extends Controller
{
    /**
     * @return Array<string, string|array>
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => [
                    'clear-opcache',
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'ips' => [
                            '127.0.0.0/8',
                            '::1',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    public function actionIndex(): Response
    {
        $resp = Yii::$app->response;
        $resp->format = 'compressive-html';
        $resp->data = $this->render('index', [
            'search' => Yii::createObject(SearchForm::class),
        ]);
        return $resp;
    }

    public function actionAbout(): string
    {
        return $this->render('about');
    }

    public function actionSchema(): string
    {
        return $this->render('schema');
    }

    public function actionRobots(): void
    {
        $resp = Yii::$app->response;
        $resp->format = Response::FORMAT_RAW;
        $resp->charset = 'UTF-8';
        $resp->headers->set('Content-Type', 'text/plain');
        $resp->content = YII_ENV_PROD
            ? implode("\n", [
                'user-agent: *',
                'allow: /',
                '',
                'user-agent: BLEXBot',
                'crawl-delay: 20',
                '',
            ])
            : implode("\n", [
                'user-agent: *',
                'disallow: /',
                '',
                'user-agent: BLEXBot',
                'crawl-delay: 20',
                '',
            ]);
    }

    public function actionSwitchLanguage(): Response
    {
        $r = Yii::$app->response;
        $r->format = Response::FORMAT_JSON;

        $req = Yii::$app->request;
        if (!$req->isPost || !$req->isAjax) {
            throw new BadRequestHttpException();
        }

        $lang = $req->post('language');
        if ($lang === 'default') {
            $r->cookies->remove(ApplicationLanguage::COOKIE_NAME);
        } elseif (is_string($lang) && ApplicationLanguage::isValidLanguageCode($lang)) {
            $r->cookies->add(
                Yii::createObject([
                    'class' => Cookie::class,
                    'expire' => strtotime('2100-01-01T00:00:00+00:00'),
                    'httpOnly' => true,
                    'name' => ApplicationLanguage::COOKIE_NAME,
                    'sameSite' => Cookie::SAME_SITE_STRICT,
                    'secure' => YII_ENV_PROD,
                    'value' => $lang,
                ]),
            );
        } else {
            throw new BadRequestHttpException();
        }

        $r->data = 'OK';
        return $r;
    }

    public function actionDisableAds(): Response
    {
        return $this->manageAds(false);
    }

    public function actionEnableAds(): Response
    {
        return $this->manageAds(true);
    }

    private function manageAds(bool $enabled): Response
    {
        $res = Yii::$app->response;
        $req = Yii::$app->request;
        if ($req->isPost) {
            $res->cookies->add(
                Yii::createObject([
                    'class' => Cookie::class,
                    'expire' => strtotime('2100-01-01T00:00:00+00:00'),
                    'httpOnly' => true,
                    'name' => 'ads-config',
                    'sameSite' => Cookie::SAME_SITE_STRICT,
                    'secure' => YII_ENV_PROD,
                    'value' => $enabled ? 'enabled' : 'disabled',
                ]),
            );
        }

        return $res->redirect(['site/index'], 302);
    }

    public function actionClearOpcache(): string
    {
        $r = Yii::$app->response;
        $r->format = Response::FORMAT_RAW;
        $r->headers->set('Content-Type', 'text/plain; charset=UTF-8');

        if (function_exists('opcache_reset')) {
            opcache_reset();
            return 'ok';
        }

        $r->statusCode = 501;
        return 'not ok';
    }
}
