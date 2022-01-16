<?php

declare(strict_types=1);

namespace app\widgets;

use Yii;
use app\assets\BootstrapIconsAsset;
use app\helpers\TypeHelper;
use app\models\Krfilter;
use app\models\Region;
use yii\base\Widget;
use yii\bootstrap5\BootstrapAsset;
use yii\bootstrap5\BootstrapPluginAsset;
use yii\helpers\Html;

final class KrfilterTargetListWidget extends Widget
{
    public Krfilter $filter;

    /** @return string */
    public function run()
    {
        BootstrapAsset::register($this->view);
        BootstrapIconsAsset::register($this->view);
        BootstrapPluginAsset::register($this->view);

        return Html::tag(
            'div',
            implode('', [
                $this->renderButton(),
                $this->renderDialog(),
            ]),
            ['id' => $this->id]
        );
    }

    private function renderButton(): string
    {
        return Html::tag(
            'div',
            Html::tag(
                'button',
                implode(' ', [
                    Yii::t('app/krfilter', 'List of countries/regions'),
                    Html::tag('span', '', ['class' => 'bi bi-window-stack']),
                ]),
                [
                    'class' => 'btn btn-outline-secondary btn-sm',
                    'data' => [
                        'bs-toggle' => 'modal',
                        'bs-target' => '#' . $this->getDialogId(),
                    ],
                    'id' => $this->getButtonId(),
                    'type' => 'button',
                ]
            ),
            [
                'class' => 'mb-2 d-grid',
            ]
        );
    }

    private function renderDialog(): string
    {
        return Html::tag(
            'div',
            Html::tag(
                'div',
                Html::tag(
                    'div',
                    implode('', [
                        $this->renderDialogHeader(),
                        $this->renderDialogBody(),
                        $this->renderDialogFooter(),
                    ]),
                    ['class' => 'modal-content']
                ),
                ['class' => 'modal-dialog modal-dialog-scrollable']
            ),
            [
                'aria' => [
                    'hidden' => 'true',
                    'labelledby' => $this->getButtonId(),
                ],
                'class' => 'modal fade',
                'data' => [
                    'bs-keyboard' => 'true',
                ],
                'id' => $this->getDialogId(),
                'tabindex' => '-1',
            ]
        );
    }

    private function renderDialogHeader(): string
    {
        return Html::tag(
            'div',
            implode('', [
                Html::tag(
                    'h5',
                    Yii::t('app/krfilter', 'Countries/regions for {filter}', [
                        'filter' => $this->filter->name,
                    ]),
                    ['class' => 'modal-title']
                ),
                Html::tag('button', '', [
                    'aria' => [
                        'label' => Yii::t('app', 'Close'),
                    ],
                    'class' => 'btn-close',
                    'data' => [
                        'bs-dismiss' => 'modal',
                    ],
                    'type' => 'button',
                ]),
            ]),
            ['class' => 'modal-header']
        );
    }

    private function renderDialogFooter(): string
    {
        return Html::tag(
            'div',
            implode('', [
                Html::tag(
                    'button',
                    implode(' ', [
                        Html::tag('span', '', ['class' => 'bi bi-x-lg']),
                        Yii::t('app', 'Close'),
                    ]),
                    [
                        'class' => 'btn btn-primary',
                        'data' => [
                            'bs-dismiss' => 'modal',
                        ],
                        'type' => 'button',
                    ]
                ),
            ]),
            ['class' => 'modal-footer']
        );
    }

    private function renderDialogBody(): string
    {
        return Html::tag(
            'div',
            Html::tag(
                'div',
                implode('', array_map(
                    fn (Region $region): string => Html::a(
                        $this->renderDialogBodyRow($region),
                        ['region/view', 'cc' => $region->id],
                        [
                            'class' => [
                                'list-group-item',
                                'list-group-item-action',
                            ],
                        ],
                    ),
                    $this->filter->regions,
                )),
                [
                    'class' => [
                        'list-group',
                        'list-group-flush',
                    ],
                ],
            ),
            ['class' => 'modal-body p-0']
        );
    }

    private function renderDialogBodyRow(Region $region): string
    {
        return Html::tag(
            'div',
            implode('', [
                Html::tag(
                    'div',
                    FlagIcon::widget(['cc' => $region->id]),
                    ['class' => 'me-2']
                ),
                Html::tag(
                    'div',
                    Html::tag('code', Html::encode($region->id)),
                    ['class' => 'me-2']
                ),
                Html::tag(
                    'div',
                    Html::encode($region->formattedName),
                    ['class' => 'flex-grow-1 w-auto me-2'],
                ),
                Html::tag(
                    'div',
                    Html::tag('span', '', ['class' => 'bi bi-chevron-right']),
                    [],
                ),
            ]),
            ['class' => 'd-flex']
        );
    }

    private function getButtonId(): string
    {
        return vsprintf('%s-btn', [
            TypeHelper::shouldBeString($this->id),
        ]);
    }

    private function getDialogId(): string
    {
        return vsprintf('%s-modal', [
            TypeHelper::shouldBeString($this->id),
        ]);
    }
}
