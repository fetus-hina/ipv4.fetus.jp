<?php

declare(strict_types=1);

namespace app\models\traits;

use Yii;
use app\assets\MontserratAsset;
use app\attributes\InjectTo;
use app\models\Region;
use yii\helpers\Html;
use yii\web\View;

/**
 * @property-read string $formattedHtmlName
 * @property-read string $formattedName
 */
#[InjectTo([
    Region::class,
])]
trait RegionNameTrait
{
    public function getFormattedHtmlName(): string
    {
        if (!$this->isJapaneseName()) {
            return Html::encode($this->name_en);
        }

        $this->prepareMontserrat();
        return \implode(' ', [
            Html::encode($this->name_ja),
            Html::tag(
                'span',
                Html::encode(
                    \sprintf('(%s)', $this->name_en),
                ),
                ['class' => 'font-montserrat'],
            ),
        ]);
    }

    public function getFormattedName(): string
    {
        return $this->isJapaneseName()
            ? \sprintf('%s (%s)', $this->name_ja, $this->name_en)
            : $this->name_en;
    }

    private function isJapaneseName(): bool
    {
        return (bool)\preg_match('/^ja\b/i', Yii::$app->language);
    }

    private function prepareMontserrat(): void
    {
        static $loaded = false;
        if (!$loaded) {
            $view = Yii::$app->view;
            if ($view instanceof View) {
                MontserratAsset::register($view);
                $loaded = true;
            }
        }
    }
}
