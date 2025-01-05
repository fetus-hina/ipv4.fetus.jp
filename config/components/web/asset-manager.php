<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

return [
    'appendTimestamp' => false,
    'bundles' => require __DIR__ . '/asset-manager/bundles.php',
    'hashCallback' => require __DIR__ . '/asset-manager/hash-callback.php',
    'linkAssets' => true,
];
