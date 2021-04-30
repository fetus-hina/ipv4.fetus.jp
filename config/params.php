<?php

declare(strict_types=1);

return (function (): array {
    return [
        'copyrightYear' => '2014-2021',
        'copyrightHolder' => 'AIZAWA Hina',

        'authorWebsite' => 'https://fetus.jp/',
        'authorTwitter' => 'fetus_hina',
        'authorGitHub' => 'fetus-hina',

        'repository' => 'https://github.com/fetus-hina/ipv4.fetus.jp',
        'report' => 'https://github.com/fetus-hina/ipv4.fetus.jp/issues',

        'gitRevision' => file_exists(__DIR__ . '/params/git-revision.php')
            ? require(__DIR__ . '/params/git-revision.php')
            : null,

        'adsense' => file_exists(__DIR__ . '/params/adsense.php')
            ? require(__DIR__ . '/params/adsense.php')
            : null,
    ];
})();
