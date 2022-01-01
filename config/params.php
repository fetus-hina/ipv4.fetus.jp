<?php

declare(strict_types=1);

return (function (): array {
    $load = function (string $fileName, $defVal = null) {
        $path = __DIR__ . '/params/' . $fileName;
        return file_exists($path) ? require($path) : $defVal;
    };

    return [
        'copyrightYear' => '2014-2022',
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
