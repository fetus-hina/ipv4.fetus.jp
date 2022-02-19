<?php

declare(strict_types=1);

namespace app\widgets;

use Yii;
use app\assets\BootstrapIconsAsset;
use app\helpers\ApplicationLanguage;
use app\helpers\TypeHelper;
use yii\base\Widget;
use yii\bootstrap5\BootstrapAsset;
use yii\bootstrap5\BootstrapPluginAsset;
use yii\helpers\Html;
use yii\web\View;

final class LanguageButton extends Widget
{
    public function run()
    {
        if (($view = $this->view) instanceof View) {
            BootstrapAsset::register($view);
            BootstrapPluginAsset::register($view);
        }

        return implode('', [
            $this->renderButton(),
            $this->renderDropdown(),
        ]);
    }

    private function renderButton(): string
    {
        return Html::a(
            implode(' ', [
                $this->bi('translate'),
                Html::encode('Language'),
            ]),
            'javascript:;',
            [
                'aria' => [
                    'expanded' => 'false',
                ],
                'class' => [
                    'btn',
                    'btn-outline-light',
                    'dropdown-toggle',
                ],
                'data' => [
                    'bs-toggle' => 'dropdown',
                ],
                'id' => $this->getButtonId(),
                'role' => 'button',
            ],
        );
    }

    private function renderDropdown(): string
    {
        return Html::tag(
            'ul',
            implode('', array_merge(
                [
                    Html::tag('li', $this->renderAutoDetectItem()),
                    Html::tag('li', $this->renderDivider()),
                ],
                array_map(
                    fn (string $html): string => Html::tag('li', $html),
                    $this->renderLanguageItems(),
                ),
            )),
            [
                'aria' => [
                    'labelledby' => $this->getButtonId(),
                ],
                'class' => 'dropdown-menu',
            ],
        );
    }

    private function renderDivider(): string
    {
        return Html::tag('hr', '', [
            'class' => 'dropdown-divider',
        ]);
    }

    private function renderAutoDetectItem(): string
    {
        return Html::a(
            implode(' ', [
                $this->bi(ApplicationLanguage::isAutoDetect() ? 'check2-square' : 'square'),
                Yii::t('app', 'Auto Detect'),
            ]),
            'javascript:;',
            [
                'class' => [
                    'dropdown-item',
                    'language-switcher',
                ],
                'data' => [
                    'language' => 'default',
                ],
            ],
        );
    }

    /** @return string[] */
    private function renderLanguageItems(): array
    {
        $langs = ApplicationLanguage::getValidLanguages();
        return array_map(
            fn (string $langCode, string $langName): string => Html::a(
                implode(' ', [
                    preg_match('/^' . preg_quote($langCode) . '\b/i', Yii::$app->language)
                        ? $this->bi('record-circle-fill')
                        : $this->bi('circle'),
                    Html::encode($langName),
                ]),
                'javascript:;',
                [
                    'class' => [
                        'dropdown-item',
                        'language-switcher',
                    ],
                    'data' => [
                        'language' => $langCode,
                    ],
                ],
            ),
            array_keys($langs),
            array_values($langs),
        );
    }

    private function getButtonId(): string
    {
        return TypeHelper::shouldBeString($this->id);
    }

    private function bi(string $icon): string
    {
        if (($view = $this->view) instanceof View) {
            BootstrapIconsAsset::register($view);
        }

        return Html::tag('span', '', [
            'class' => [
                'bi',
                "bi-{$icon}",
            ],
        ]);
    }
}
