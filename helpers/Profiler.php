<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

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
