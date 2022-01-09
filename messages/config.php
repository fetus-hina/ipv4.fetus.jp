<?php

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
