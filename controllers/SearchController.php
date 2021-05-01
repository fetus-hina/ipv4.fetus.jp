<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\models\SearchForm;
use app\models\SearchResult;
use yii\web\Controller;

class SearchController extends Controller
{
    public function actionIndex(): string
    {
        /** @var ?SearchResult */
        $result = null;
        $form = Yii::createObject(SearchForm::class);
        if (
            $form->load(Yii::$app->request->get()) &&
            $form->validate()
        ) {
            $result = $form->search();
        }

        return $this->render('index', [
            'form' => $form,
            'result' => $result,
        ]);
    }

    public function actionCompat(string $query): void
    {
        $this->redirect(['search/index', 'query' => $query], 301);
    }
}
