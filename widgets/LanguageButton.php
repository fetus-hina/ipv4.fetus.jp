<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\widgets;

use Yii;
use app\assets\LatinFontAsset;
use app\helpers\ApplicationLanguage;
use app\helpers\TypeHelper;
use app\models\Language;
use yii\base\Widget;
use yii\bootstrap5\BootstrapAsset;
use yii\bootstrap5\BootstrapPluginAsset;
use yii\helpers\Html;
use yii\web\View;

use function array_map;
use function array_merge;
use function implode;
use function preg_match;
use function preg_quote;
use function vsprintf;

final class LanguageButton extends Widget
{
    public function run(): string
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
                Icon::translate(),
                $this->montserrat(Html::encode('Language')),
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
                ApplicationLanguage::isAutoDetect()
                    ? Icon::checkboxChecked()
                    : Icon::checkboxUnchecked(),
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
        $langs = ApplicationLanguage::getValidLanguagesEx();
        return array_map(
            fn (Language $language): string => Html::a(
                implode(' ', [
                    preg_match('/^' . preg_quote($language->id, '/') . '\b/i', Yii::$app->language)
                        ? Icon::radioChecked()
                        : Icon::radioUnchecked(),
                    $this->renderLanguageName($language),
                ]),
                'javascript:;',
                [
                    'class' => [
                        'dropdown-item',
                        'language-switcher',
                    ],
                    'data' => [
                        'language' => $language->id,
                    ],
                ],
            ),
            $langs,
        );
    }

    private function renderLanguageName(Language $lang): string
    {
        $nativeName = match (true) {
            ApplicationLanguage::isLatin($lang->id) => $this->montserrat(
                Html::encode($lang->native_name),
            ),
            ApplicationLanguage::isJapanese($lang->id) => Html::tag(
                'span',
                Html::encode($lang->native_name),
                ['class' => 'font-japanese'],
            ),
            default => Html::encode($lang->native_name),
        };

        return $lang->native_name === $lang->english_name
            ? $nativeName
            : vsprintf('%s %s', [
                $nativeName,
                $this->montserrat(
                    Html::tag(
                        'span',
                        Html::encode("({$lang->english_name})"),
                        ['class' => 'small text-muted'],
                    ),
                ),
            ]);
    }

    private function getButtonId(): string
    {
        return TypeHelper::shouldBeString($this->id);
    }

    private function montserrat(string $html): string
    {
        if (($view = $this->view) instanceof View) {
            LatinFontAsset::register($view);
        }

        return Html::tag('span', $html, ['class' => 'font-montserrat']);
    }
}
