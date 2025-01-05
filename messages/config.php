<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

return (function (): array {
    $appPath = dirname(__DIR__);

    return [
        'catalog' => 'messages',
        'except' => [
            '.git',
            '.gitignore',
            '.gitkeep',
            '.hgignore',
            '.hgkeep',
            '.svn',
            '/deploy.php',
            '/gii',
            '/messages',
            '/migrations',
            '/node_modules',
            '/runtime',
            '/vendor',
        ],
        'format' => 'po',
        'ignoreCategories' => ['yii'],
        'languages' => ['ja'],
        'markUnused' => false,
        'messagePath' => $appPath . DIRECTORY_SEPARATOR . 'messages',
        'only' => ['*.php'],
        'overwrite' => true,
        'phpDocBlock' => null,
        'phpFileHeader' => '',
        'removeUnused' => true,
        'sort' => true,
        'sourcePath' => $appPath,
        'translator' => 'Yii::t',
    ];
})();
