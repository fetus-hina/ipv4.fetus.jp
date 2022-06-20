<?php

declare(strict_types=1);

namespace app\widgets;

use Closure;
use Yii;
use app\assets\TooltipAsset;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

final class Tooltip extends Widget
{
    public const PLACEMENT_BOTTOM = 'bottom';
    public const PLACEMENT_LEFT = 'left';
    public const PLACEMENT_RIGHT = 'right';
    public const PLACEMENT_TOP = 'top';

    public mixed $content = '';

    public Closure|array|string $format = 'raw';

    public string $title = '';

    public bool $titleIsHtml = false;

    /**
     * @var 'bottom'|'left'|'right'|'top' $placement
     */
    public $placement = self::PLACEMENT_BOTTOM;

    /**
     * @var array<string, string|array<string, string>> $options
     */
    public array $options = [
        'tag' => 'span',
    ];

    public function run(): string
    {
        if (($view = $this->view) instanceof View) {
            TooltipAsset::register($view);
        }

        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag');
        return Html::tag(
            \is_string($tag) ? $tag : 'span',
            Yii::$app->formatter->format($this->content, $this->format),
            ArrayHelper::merge(
                $options,
                [
                    'data' => [
                        'bs-html' => $this->titleIsHtml ? 'true' : false,
                        'bs-placement' => $this->placement,
                        'bs-toggle' => 'tooltip',
                    ],
                    'title' => $this->title,
                ],
            ),
        );
    }
}
