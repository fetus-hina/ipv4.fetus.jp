<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\widgets;

use Yii;
use app\widgets\ads\BrandAd;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use function implode;
use function vsprintf;

final class FetusHeader extends Widget
{
    public function run(): string
    {
        $authorWebsite = ArrayHelper::getValue(Yii::$app->params, 'authorWebsite');
        if ($authorWebsite !== 'https://fetus.jp/') {
            return '';
        }

        return Html::tag(
            'header',
            Html::tag(
                'div',
                implode('', [
                    $this->renderHeading($authorWebsite),
                    $this->renderAd(),
                ]),
                [
                    'class' => [
                        'container',
                        'position-relative',
                    ],
                ],
            ),
            [
                'class' => [
                    'mb-4',
                ],
            ],
        );
    }

    private function renderHeading(string $authorWebsite): string
    {
        return Html::tag(
            'h1',
            Html::a(
                Html::encode('fetus'),
                $this->getCurrentPageId() === 'site/index'
                    ? $authorWebsite
                    : ['site/index'],
                [],
            ),
        );
    }

    private function renderAd(): string
    {
        return BrandAd::widget();
    }

    private function getCurrentPageId(): string
    {
        $controller = Yii::$app->controller;
        return vsprintf('%s/%s', [
            $controller->id,
            $controller->action->id ?? 'UNKNOWN',
        ]);
    }
}
