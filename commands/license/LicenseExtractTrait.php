<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\license;

use DirectoryIterator;
use Yii;
use app\helpers\TypeHelper;
use stdClass;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Json;

use function array_shift;
use function copy;
use function dirname;
use function escapeshellarg;
use function file_exists;
use function file_get_contents;
use function fwrite;
use function implode;
use function pathinfo;
use function preg_match;
use function preg_replace;
use function str_replace;
use function strcasecmp;
use function strcmp;
use function strnatcasecmp;
use function trim;
use function usort;
use function vsprintf;

use const PATHINFO_FILENAME;
use const STDERR;

trait LicenseExtractTrait
{
    use Helper;

    public function actionExtract(): int
    {
        $packages = $this->getPackages();
        $this->extractPackages($packages);
        return 0;
    }

    public function actionCleanExtracted(): int
    {
        $baseDir = TypeHelper::shouldBeString(Yii::getAlias('@app/data/licenses/composer'));
        if (file_exists($baseDir)) {
            FileHelper::removeDirectory($baseDir);
        }

        return 0;
    }

    // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
    private function getPackages(): array
    {
        $cmdline = vsprintf('/usr/bin/env %s --no-interaction --no-plugins license --format=%s', [
            escapeshellarg(TypeHelper::shouldBeString(Yii::getAlias('@app/composer.phar'))),
            escapeshellarg('json'),
        ]);
        $json = TypeHelper::shouldBeArray(
            Json::decode(
                TypeHelper::shouldBeString($this->execCommand($cmdline)),
            ),
        );
        return TypeHelper::shouldBeArray(
            ArrayHelper::getValue($json, 'dependencies'),
        );
    }

    private function extractPackages(array $packages): void
    {
        foreach ($packages as $name => $info) {
            $this->extractPackage(
                isset($info['version']) && trim((string)$info['version']) !== ''
                    ? "{$name}@{$info['version']}"
                    : $name,
                Yii::getAlias('@app/vendor') . '/' . $name,
            );
        }
    }

    private function extractPackage(string $packageName, string $baseDir): bool
    {
        if (!file_exists($baseDir)) {
            fwrite(STDERR, "license/extract: Directory does not exists: $packageName\n");
            return false;
        }

        if (!$path = $this->findLicense($packageName, $baseDir)) {
            fwrite(STDERR, "license/extract: license file does not exists: $baseDir\n");
            return false;
        }

        $distPath = implode('/', [
            Yii::getAlias('@app/data/licenses/composer'),
            $this->sanitize($packageName) . '-LICENSE.txt',
        ]);
        if (!FileHelper::createDirectory(dirname($distPath))) {
            fwrite(
                STDERR,
                'license/extract: could not create directory: ' . dirname($distPath) . "\n",
            );
            return false;
        }
        copy($path, $distPath);
        return true;
    }

    private function findLicense(string $name, string $dir): ?string
    {
        $precedence = [
            '/^LICEN[CS]E$/i',
            '/^LICEN[CS]E\-\w+$/i', // e.g. LICENSE-MIT
            '/^MIT-LICEN[CS]E$/i',
            '/^COPYING$/i',
            '/^README$/i',
        ];

        $files = [];
        $it = new DirectoryIterator($dir);
        foreach ($it as $entry) {
            if ($entry->isDot() || $entry->isDir()) {
                continue;
            }

            $path = $entry->getPathname();
            $basename = $entry->getBasename();
            $filename = pathinfo($basename, PATHINFO_FILENAME);

            foreach ($precedence as $i => $regexp) {
                if (preg_match($regexp, $filename)) {
                    $files[] = (object)[
                        'precedence' => $i,
                        'basename' => $basename,
                        'path' => $path,
                    ];
                }
            }
        }

        if (!$files) {
            fwrite(STDERR, "license/extract: no license file detected on {$name}\n");
            return null;
        }

        usort($files, fn (stdClass $a, stdClass $b): int => $a->precedence <=> $b->precedence
                ?: strnatcasecmp($a->basename, $b->basename)
                ?: strcasecmp($a->basename, $b->basename)
                ?: strcmp($a->basename, $b->basename));

        while ($files) {
            $info = array_shift($files);
            if ($this->hasLicense($info->path)) {
                return $info->path;
            }
        }
        return null;
    }

    private function hasLicense(string $path): bool
    {
        $text = TypeHelper::shouldBeString(file_get_contents($path, false));
        return (bool)preg_match('/license|copyright/i', $text);
    }

    private function sanitize(string $packageName): string
    {
        $packageName = TypeHelper::shouldBeString(
            preg_replace(
                '/[^!#$%()+,.\/-9@-Z_a-z]+/',
                '-',
                $packageName,
            ),
        );
        $packageName = str_replace('/../', '/', $packageName);
        $packageName = str_replace('/./', '/', $packageName);
        return $packageName;
    }
}
