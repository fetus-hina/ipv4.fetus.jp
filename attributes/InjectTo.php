<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

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
