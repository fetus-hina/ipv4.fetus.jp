<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\widgets;

use LogicException;
use RuntimeException;
use Yii;
use app\models\SearchForm;
use yii\base\Widget;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

use function implode;
use function ob_get_clean;
use function ob_start;

final class SearchCard extends Widget
{
    public ?SearchForm $form = null;

    public function run(): string
    {
        if (($form = $this->form) === null) {
            throw new LogicException();
        }

        return $this->renderCard($form);
    }

    private function renderCard(SearchForm $form): string
    {
        return Html::tag(
            'div',
            implode('', [
                $this->renderCardHeader(),
                $this->renderCardBody($form),
            ]),
            [
                'class' => [
                    'card',
                    'border-primary',
                ],
            ],
        );
    }

    private function renderCardHeader(): string
    {
        return Html::tag(
            'div',
            Html::encode(Yii::t('app/search', 'Search IP Address')),
            [
                'class' => [
                    'bg-primary',
                    'card-header',
                    'text-white',
                ],
            ],
        );
    }

    private function renderCardBody(SearchForm $form): string
    {
        return Html::tag(
            'div',
            $this->renderForm($form),
            [
                'class' => [
                    'card-body',
                ],
            ],
        );
    }

    private function renderForm(SearchForm $form): string
    {
        ob_start();
        $this->putForm($form);
        if (($content = ob_get_clean()) === false) {
            throw new RuntimeException();
        }
        return $content;
    }

    private function putForm(SearchForm $model): void
    {
        $form = ActiveForm::begin([
            'action' => ['search/index'],
            'method' => 'get',
        ]);
        echo Html::tag(
            'div',
            (string)$form->field($model, 'query')
                ->label(false)
                ->textInput([
                    'placeholder' => Yii::t('app/search', 'e.g., {exampleIP}', [
                        'exampleIP' => '203.0.113.1',
                    ]),
                ]),
            [
                'class' => [
                    'mb-2',
                ],
            ],
        );
        echo Html::tag(
            'div',
            $this->renderSubmitButton(),
            [
                'class' => [
                    'd-grid',
                ],
            ],
        );
        ActiveForm::end();
    }

    private function renderSubmitButton(): string
    {
        return Html::submitButton(
            implode(' ', [
                Icon::search(),
                Html::encode(Yii::t('app/search', 'Search')),
            ]),
            [
                'class' => [
                    'btn',
                    'btn-primary',
                ],
            ],
        );
    }
}
