<?php

declare(strict_types=1);

namespace app\widgets;

use app\assets\BootstrapIconsAsset;
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
                    Html::encode('対象国/地域一覧'),
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
                    Html::encode('対象の国/地域 - ' . $this->filter->name),
                    ['class' => 'modal-title']
                ),
                Html::tag('button', '', [
                    'aria' => [
                        'label' => '閉じる',
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
                Html::tag('button', Html::encode('閉じる'), [
                    'class' => 'btn btn-primary',
                    'data' => [
                        'bs-dismiss' => 'modal',
                    ],
                    'type' => 'button',
                ]),
            ]),
            ['class' => 'modal-footer']
        );
    }

    private function renderDialogBody(): string
    {
        return Html::tag(
            'div',
            Html::tag(
                'ul',
                implode('', array_map(
                    fn ($region) => Html::tag(
                        'li',
                        $this->renderDialogBodyRow($region),
                        ['class' => 'list-group-item']
                    ),
                    $this->filter->regions,
                )),
                ['class' => 'list-group list-group-flush']
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
                    vsprintf('%s %s', [
                        Html::encode($region->name_ja),
                        Html::tag(
                            'span',
                            Html::encode(sprintf('(%s)', $region->name_en)),
                            [
                                'class' => 'text-muted small',
                                'lang' => 'en-US',
                            ]
                        ),
                    ]),
                    ['class' => 'flex-grow-1 w-auto me-2']
                ),
                Html::tag(
                    'div',
                    Html::a(
                        Html::tag('span', '', ['class' => 'bi bi-info-circle']),
                        ['region/view', 'cc' => $region->id],
                        []
                    ),
                    []
                ),
            ]),
            ['class' => 'd-flex']
        );
    }

    private function getButtonId(): string
    {
        return sprintf('%s-btn', $this->id);
    }

    private function getDialogId(): string
    {
        return sprintf('%s-modal', $this->id);
    }
}
