<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols

declare(strict_types=1);

namespace app\attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Implement
{
    public array $interfaces;

    public function __construct(array $interfaces)
    {
        $this->interfaces = $interfaces;
    }
}
