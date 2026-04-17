<?php // phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\helpers\TypeHelper;
use yii\web\Application;

if (
    file_exists(__DIR__ . '/../.production') ||
    // phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
    !in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)
) {
    die('You are not allowed to access this file.');
}

define('YII_DEBUG', true);
define('YII_ENV', 'test');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/../config/bootstrap.php';

$config = TypeHelper::shouldBeArray(require __DIR__ . '/../config/test.php', TypeHelper::ARRAY_ASSOC);
$stringKeyed = [];
foreach ($config as $k => $v) {
    if (is_string($k)) {
        $stringKeyed[$k] = $v;
    }
}
(new Application($stringKeyed))->run();
