<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\license;

use Yii;
use app\helpers\TypeHelper;

use function chdir;
use function exec;
use function fwrite;
use function getcwd;
use function implode;

use const STDERR;

trait Helper
{
    protected function execCommand(string $cmdline): ?string
    {
        $cwd = getcwd();
        if ($cwd === false) {
            return $this->execCommandImpl($cmdline);
        }

        try {
            chdir(
                TypeHelper::shouldBeString(Yii::getAlias('@app')),
            );
            return $this->execCommandImpl($cmdline);
        } finally {
            @chdir($cwd); // restore
        }
    }

    private function execCommandImpl(string $cmdline): ?string
    {
        $lines = null;
        $status = null;
        @exec($cmdline, $lines, $status);
        if ($status !== 0) {
            fwrite(STDERR, "Failed to execute $cmdline (status={$status})\n");
            return null;
        }
        return implode("\n", $lines);
    }
}
