<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols

declare(strict_types=1);

namespace app\attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class InjectTo
{
    public array $targetClasses;

    public function __construct(array $targetClasses)
    {
        $this->targetClasses = $targetClasses;
    }
}
