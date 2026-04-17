<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\helpers\TypeHelper;

return array_merge(TypeHelper::shouldBeArray(require __DIR__ . '/../web/url-manager.php'), [
    'showScriptName' => true,
]);
