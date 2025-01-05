<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\controllers;

use Throwable;
use Yii;
use app\helpers\TypeHelper;
use app\models\SearchForm;
use app\models\SearchResult;
use yii\web\Controller;

class SearchController extends Controller
{
    public function actionIndex(): string
    {
        /** @var ?SearchResult $result */
        $result = null;
        $form = Yii::createObject(SearchForm::class);
        try {
            if (
                $form->load(
                    TypeHelper::shouldBeArray(
                        Yii::$app->request->get(),
                        TypeHelper::ARRAY_ASSOC,
                    ),
                ) &&
                $form->validate()
            ) {
                $result = $form->search();
            }
        } catch (Throwable $e) {
            $form = Yii::createObject(SearchForm::class);
            $result = null;
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
