<?php

declare(strict_types=1);

namespace app\models\traits;

use Yii;
use app\attributes\InjectTo;
use app\models\Region;
use yii\helpers\Html;

use function preg_match;
use function sprintf;

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
        return Html::encode($this->getFormattedName());
    }

    public function getFormattedName(): string
    {
        return $this->isJapaneseName()
            ? sprintf('%s (%s)', $this->name_ja, $this->name_en)
            : $this->name_en;
    }

    private function isJapaneseName(): bool
    {
        return (bool)preg_match('/^ja\b/i', Yii::$app->language);
    }
}
