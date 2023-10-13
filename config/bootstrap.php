<?php

declare(strict_types=1);

use app\widgets\Icon;
use yii\base\Widget;
use yii\bootstrap5\LinkPager as BSLinkPager;
use yii\data\Pagination;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;

Widget::$autoIdPrefix = vsprintf('w-%s-', [
    substr(hash('sha256', uniqid('', true), false), 0, 12),
]);

Yii::$container->set(Pagination::class, [
    'defaultPageSize' => 200,
    'pageSizeLimit' => [50, 1000],
]);

Yii::$container->set(GridView::class, [
    'layout' => '{summary}{pager}{items}{pager}',
    'summary' => Html::tag('div', '{begin}-{end} of {totalCount} rows', [
        'class' => 'summary text-end font-italic small text-muted',
        'style' => [
            'margin' => '0 1.25rem 0.75rem',
        ],
    ]),
    'tableOptions' => [
        'class' => 'table table-striped table-borderless table-hover table-sm text-nowrap',
    ],
    'headerRowOptions' => [
        'class' => 'text-center',
    ],
]);

Yii::$container->set(LinkPager::class, [
    'class' => BSLinkPager::class,
    'maxButtonCount' => 3,
    'options' => [
        'tag' => 'nav',
    ],
    'listOptions' => [
        'class' => 'pagination justify-content-center',
    ],
    'nextPageLabel' => implode('', [
        Icon::pagerNext(),
        Html::tag('span', Html::encode('Next'), ['class' => 'visually-hidden']),
    ]),
    'prevPageLabel' => implode('', [
        Icon::pagerPrev(),
        Html::tag('span', Html::encode('Previous'), ['class' => 'visually-hidden']),
    ]),
    'firstPageLabel' => implode('', [
        Icon::pagerFirst(),
        Html::tag('span', Html::encode('First'), ['class' => 'visually-hidden']),
    ]),
    'lastPageLabel' => implode('', [
        Icon::pagerLast(),
        Html::tag('span', Html::encode('Last'), ['class' => 'visually-hidden']),
    ]),
]);

Yii::$container->set(Pjax::class, [
    'timeout' => 2500,
]);
