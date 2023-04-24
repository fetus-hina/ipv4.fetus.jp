<?php

declare(strict_types=1);

namespace app\widgets;

use app\assets\MontserratAsset;
use yii\base\Widget;
use yii\helpers\Html;
use yii\web\View;

use function implode;

final class StandWithUkraine extends Widget
{
    private const CC_UKRAINE = 'ua';

    private const UKRAINE_BLUE = '#0057b7';
    private const UKRAINE_YELLOW = '#ffd700';

    public string $marginClass = 'mb-3';

    public function run(): string
    {
        if (($view = $this->view) instanceof View) {
            MontserratAsset::register($view);
        }

        return Html::tag(
            'div',
            implode('', [
                $this->renderFlag(),
                $this->renderText(),
            ]),
            [
                'class' => [
                    'd-flex',
                    'flex-row',
                    'align-items-center',
                    'lh-1',
                    $this->marginClass,
                ],
                'lang' => 'en',
                'style' => [
                    'font-family' => 'Montserrat,sans-serif',
                ],
            ],
        );
    }

    private function renderFlag(): string
    {
        return Html::tag(
            'div',
            FlagIcon::widget([
                'cc' => self::CC_UKRAINE,
            ]),
            [
                'class' => [
                    'me-3',
                ],
                'style' => [
                    'font-size' => '3rem',
                ],
            ],
        );
    }

    private function renderText(): string
    {
        return Html::tag(
            'div',
            implode('', [
                $this->renderTextStandWith(),
                $this->renderTextUkraine(),
            ]),
            [
                'class' => [
                    'flex-fill',
                ],
                'style' => [
                    'font-size' => '0.75rem',
                ],
            ],
        );
    }

    private function renderTextStandWith(): string
    {
        return $this->renderTextImpl(
            '#StandWith',
            self::UKRAINE_BLUE,
            self::UKRAINE_YELLOW,
        );
    }

    private function renderTextUkraine(): string
    {
        return $this->renderTextImpl(
            'Ukraine',
            self::UKRAINE_YELLOW,
            self::UKRAINE_BLUE,
        );
    }

    private function renderTextImpl(string $text, string $bgColor, string $fgColor): string
    {
        return Html::tag(
            'span',
            Html::encode($text),
            [
                'class' => [
                    'px-1',
                    'py-1',
                ],
                'style' => [
                    'background-color' => $bgColor,
                    'color' => $fgColor,
                ],
            ],
        );
    }
}
