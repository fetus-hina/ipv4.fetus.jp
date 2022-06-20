<?php

declare(strict_types=1);

namespace app\models\traits;

use Yii;
use app\attributes\InjectTo;
use app\models\Region;

/**
 * @property-read string $formattedName
 */
#[InjectTo([
    Region::class,
])]
trait RegionNameTrait
{
    public function getFormattedName(): string
    {
        return \preg_match('/^ja\b/i', Yii::$app->language)
            ? \sprintf('%s (%s)', $this->name_ja, $this->name_en)
            : $this->name_en;
    }
}
