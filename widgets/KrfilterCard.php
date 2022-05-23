<?php

declare(strict_types=1);

namespace app\widgets;

use LogicException;
use Yii;
use app\models\DownloadTemplate;
use app\models\Krfilter;
use app\models\Region;
use yii\base\Widget;
use yii\helpers\Html;

final class KrfilterCard extends Widget
{
    public ?Krfilter $model = null;

    public function run(): string
    {
        if (($model = $this->model) === null) {
            throw new LogicException();
        }

        return Html::tag(
            'div',
            $this->renderCard($model),
            [
                'class' => [
                    'col-12',
                    'col-sm-6',
                    'col-lg-4',
                    'col-xl-3',
                    'mb-4',
                ],
            ],
        );
    }

    private function renderCard(Krfilter $model): string
    {
        return Html::tag(
            'div',
            implode('', [
                $this->renderCardHeader($model),
                $this->renderCardBody($model),
            ]),
            [
                'class' => [
                    'card',
                    'card-primary',
                    'h-100',
                ],
            ],
        );
    }

    private function renderCardHeader(Krfilter $model): string
    {
        return Html::tag(
            'div',
            Html::encode($model->name),
            [
                'class' => [
                    'bg-primary',
                    'card-header',
                    'text-white',
                ],
            ],
        );
    }

    private function renderCardBody(Krfilter $model): string
    {
        return Html::tag(
            'div',
            implode('', [
                preg_match('/^rufilter/i', $model->name)
                    ? StandWithUkraine::widget()
                    : '',
                $this->renderRegionList($model),
                KrfilterTargetListWidget::widget(['filter' => $model]),
                DownloadButtons::widget([
                    'downloadLinkCreator' => fn (?DownloadTemplate $template): array => [
                        'krfilter/plain',
                        'id' => $model->id,
                        'template' => $template?->key,
                    ],
                    'enableIpv4bycc' => false,
                ]),
            ]),
            [
                'class' => [
                    'card-body',
                    'd-flex',
                    'flex-column',
                ],
            ],
        );
    }

    private function renderRegionList(Krfilter $model): string
    {
        return Html::tag(
            'p',
            Yii::t('app/krfilter', 'Consolidated list for<br>{list}', [
                'list' => implode(' ', array_map(
                    fn (Region $region): string => implode('', [
                        Tooltip::widget([
                            'content' => FlagIcon::widget(['cc' => $region->id]),
                            'format' => 'raw',
                            'title' => $region->formattedName,
                        ]),
                        Html::tag(
                            'span',
                            preg_match('/^ja\b/i', Yii::$app->language)
                                ? Html::encode("({$region->name_ja}), ")
                                : Html::encode("({$region->name_en}), "),
                            [
                                'class' => [
                                    'visually-hidden',
                                ],
                            ],
                        ),
                    ]),
                    $this->getRegionList($model),
                )),
            ]),
            [
                'class' => [
                    'mb-2',
                    'flex-grow-1',
                ],
            ],
        );
    }

    /**
     * @return Region[]
     */
    private function getRegionList(Krfilter $model): array
    {
        $regions = $model->regions;
        usort(
            $regions,
            fn (Region $a, Region $b): int => strcmp($a->id, $b->id),
        );
        return $regions;
    }
}
