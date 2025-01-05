<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\widgets;

use LogicException;
use Yii;
use app\models\Region;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use function implode;
use function is_string;
use function preg_match;

final class RegionHeading extends Widget
{
    private const PADDING = 'me-2';

    public ?Region $region = null;

    public array $options = [
        'tag' => 'div',
    ];

    public function run(): string
    {
        if (($region = $this->region) === null) {
            throw new LogicException();
        }

        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag', null);

        return Html::tag(
            is_string($tag) ? $tag : 'div',
            implode('', [
                $this->renderFlag($region),
                $this->renderName($region),
            ]),
            $options,
        );
    }

    private function renderFlag(Region $region): string
    {
        return Html::tag(
            'span',
            FlagIcon::widget([
                'cc' => $region->id,
            ]),
            [
                'class' => self::PADDING,
            ],
        );
    }

    private function renderName(Region $region): string
    {
        return preg_match('/^ja\b/i', Yii::$app->language)
            ? $this->renderNameJa($region)
            : $this->renderNameEn($region);
    }

    private function renderNameEn(Region $region): string
    {
        return Html::encode($region->name_en);
    }

    private function renderNameJa(Region $region): string
    {
        return implode('', [
            Html::tag(
                'span',
                Html::encode($region->name_ja),
                [
                    'class' => self::PADDING,
                ],
            ),
            Html::tag(
                'small',
                Html::encode($region->name_en),
                [
                    'class' => 'text-muted',
                    'lang' => 'en',
                ],
            ),
        ]);
    }
}
