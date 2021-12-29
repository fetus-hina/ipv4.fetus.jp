<?php

declare(strict_types=1);

namespace app\widgets;

use LogicException;
use app\assets\FlagIconsAsset;
use yii\base\Widget;
use yii\helpers\Html;
use yii\web\View;

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
            $this->text($cc),
            [
                'class' => [
                    'flag-icon',
                    "flag-icon-{$cc}",
                ],
            ],
        );
    }

    private function text(string $cc): string
    {
        return Html::tag(
            'span',
            implode('', array_map(
                fn (string $c): string => (string)mb_chr(0x1F1E6 + ord($c) - ord('a'), 'UTF-8'),
                [
                    substr($cc, 0, 1),
                    substr($cc, 1, 1),
                ]
            )),
            [
                'class' => 'visually-hidden',
            ],
        );
    }
}
