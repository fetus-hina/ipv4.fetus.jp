<?php

declare(strict_types=1);

namespace app\widgets;

use LogicException;
use app\assets\FlagIconsAsset;
use app\helpers\Unicode;
use yii\base\Widget;
use yii\helpers\Html;
use yii\web\View;

use function preg_match;
use function strtolower;
use function trim;

final class FlagIcon extends Widget
{
    public string $cc = 'xx';

    public function run(): string
    {
        $cc = trim(strtolower($this->cc));

        if (!preg_match('/^[a-zA-Z]{2}$/', $cc)) {
            throw new LogicException("Invalid CC: {$cc}");
        }

        if (($view = $this->view) instanceof View) {
            FlagIconsAsset::register($view);
        }

        return Html::tag(
            'span',
            $this->renderContent($cc),
            [
                'class' => [
                    'flag-icon',
                    "flag-icon-{$cc}",
                ],
            ],
        );
    }

    private function renderContent(string $cc): string
    {
        return Html::tag(
            'span',
            Unicode::asciiToRegionalIndicator($cc),
            [
                'class' => [
                    'visually-hidden',
                ],
            ],
        );
    }
}
