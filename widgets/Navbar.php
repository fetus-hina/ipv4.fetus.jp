<?php

declare(strict_types=1);

namespace app\widgets;

use Yii;
use yii\base\Widget;
use yii\bootstrap5\BootstrapPluginAsset;
use yii\helpers\Html;
use yii\web\View;

final class Navbar extends Widget
{
    /**
     * @inheritdoc
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        if (($view = $this->view) instanceof View) {
            BootstrapPluginAsset::register($view);
        }
    }

    public function run(): string
    {
        return Html::tag(
            'div',
            $this->renderNavbar(),
            [
                'class' => [
                    'container',
                ],
            ],
        );
    }

    private function renderNavbar(): string
    {
        return Html::tag(
            'nav',
            implode('', [
                $this->renderBrand(),
                $this->renderToggler(),
                $this->renderLinks(),
            ]),
            [
                'class' => [
                    'bg-dark',
                    'navbar',
                    'navbar-dark',
                    'navbar-expand-md',
                    'px-3',
                    'rounded',
                    'shadow',
                ],
                'role' => 'navigation',
            ],
        );
    }

    private function renderBrand(): string
    {
        return Html::a(
            Html::encode(Yii::$app->name),
            ['site/index'],
            [
                'class' => [
                    'navbar-brand',
                ],
            ],
        );
    }

    private function renderToggler(): string
    {
        return Html::button(
            $this->renderTogglerIcon(),
            [
                'aria' => [
                    'controls' => $this->getLinksId(),
                    'expanded' => 'true',
                    'label' => Yii::t('app/navbar', 'Toggle Navbar'),
                ],
                'class' => [
                    'navbar-toggler',
                ],
                'data' => [
                    'bs-target' => sprintf('#%s', $this->getLinksId()),
                    'bs-toggle' => 'collapse',
                ],
                'type' => 'button',
            ]
        );
    }

    private function renderTogglerIcon(): string
    {
        return Html::tag(
            'span',
            '',
            [
                'class' => [
                    'navbar-toggler-icon',
                ],
            ],
        );
    }

    private function renderLinks(): string
    {
        return Html::tag(
            'div',
            implode('', [
                $this->renderLinksLeft(),
                $this->renderLinksRight(),
            ]),
            [
                'class' => [
                    'collapse',
                    'navbar-collapse',
                ],
                'id' => $this->getLinksId(),
                'style' => [
                    'transform' => 'none',
                ],
            ],
        );
    }

    private function renderLinksLeft(): string
    {
        return Html::tag(
            'ul',
            implode('', [
                Html::tag(
                    'li',
                    Html::a(
                        Html::encode(Yii::t('app/navbar', 'Top')),
                        ['site/index'],
                        [
                            'class' => [
                                'nav-link',
                            ],
                        ],
                    ),
                    [
                        'class' => [
                            'nav-item',
                        ],
                    ],
                ),
                Html::tag(
                    'li',
                    Html::a(
                        Html::encode(
                            Yii::t('app/navbar', 'krfilter / eufilter (for GDPR)'),
                        ),
                        ['krfilter/view'],
                        [
                            'class' => [
                                'nav-link',
                            ],
                        ],
                    ),
                    [
                        'class' => [
                            'nav-item',
                        ],
                    ],
                ),
            ]),
            [
                'class' => [
                    'me-auto',
                    'navbar-nav',
                ],
            ],
        );
    }

    private function renderLinksRight(): string
    {
        return Html::tag(
            'ul',
            Html::tag(
                'li',
                LanguageButton::widget(),
                [
                    'class' => [
                        'nav-item',
                        'dropdown',
                    ],
                ],
            ),
            [
                'class' => [
                    'ms-auto',
                    'navbar-nav',
                ],
            ],
        );
    }

    private function getLinksId(): string
    {
        return sprintf('%s-links', (string)$this->id);
    }
}
