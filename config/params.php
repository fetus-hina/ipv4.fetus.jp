<?php

declare(strict_types=1);

use app\helpers\FileHelper;

return (function (): array {
    $load = fn (string $fileName, mixed $defVal = null): mixed => FileHelper::requireIfExists(
        implode(DIRECTORY_SEPARATOR, [
            __DIR__,
            'params',
            $fileName,
        ]),
        $defVal,
    );

    return [
        'copyrightYear' => '2014-2023',
        'copyrightHolder' => 'AIZAWA Hina',

        'authorWebsite' => 'https://fetus.jp/',
        'authorTwitter' => 'fetus_hina',
        'authorGitHub' => 'fetus-hina',

        'repository' => 'https://github.com/fetus-hina/ipv4.fetus.jp',
        'report' => 'https://github.com/fetus-hina/ipv4.fetus.jp/issues',

        'adsense' => $load('adsense.php'),
        'dbUpdateTimestamp' => $load('database-update-timestamp.php'),
        'gitRevision' => $load('git-revision.php'),
    ];
})();
