<?php

declare(strict_types=1);

return [
    'appendTimestamp' => false,
    'bundles' => require __DIR__ . '/asset-manager/bundles.php',
    'hashCallback' => require __DIR__ . '/asset-manager/hash-callback.php',
    'linkAssets' => true,
];
