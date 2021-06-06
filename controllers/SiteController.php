<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\models\SearchForm;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;

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

    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    public function actionIndex(): string
    {
        return $this->render('index', [
            'search' => Yii::createObject(SearchForm::class),
        ]);
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
