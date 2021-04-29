<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\models\SearchForm;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;

class SiteController extends Controller
{
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
            ])
            : implode("\n", [
                'user-agent: *',
                'disallow: /',
                '',
            ]);
    }
}
