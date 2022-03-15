<?php

declare(strict_types=1);

namespace app\helpers;

use Yii;

final class Profiler
{
    public function __construct(
        private string $token,
        private string $category,
    ) {
        Yii::beginProfile($token, $category);
    }

    public function __destruct()
    {
        Yii::endProfile($this->token, $this->category);
    }

    private function __clone()
    {
    }
}
