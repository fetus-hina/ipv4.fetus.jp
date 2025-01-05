<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\license;

use Collator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Yii;
use app\helpers\TypeHelper;
use stdClass;
use yii\base\Action;
use yii\helpers\Html;

use function call_user_func;
use function file_get_contents;
use function ltrim;
use function preg_match;
use function preg_replace;
use function strlen;
use function substr;
use function trim;
use function usort;

final class LicenseAction extends Action
{
    public string $view = '//license/license';
    public string $title = 'Licenses';
    public string $directory;
    public array $pageUrl;

    /**
     * @return string
     */
    public function run()
    {
        return $this->controller->render($this->view, [
            'depends' => $this->loadDepends(),
            'pageUrl' => $this->pageUrl,
            'title' => $this->title,
        ]);
    }

    /**
     * @return stdClass[]
     */
    private function loadDepends(): array
    {
        $c = TypeHelper::instanceOf(Collator::create('en_US'), Collator::class);
        $c->setAttribute(Collator::NUMERIC_COLLATION, Collator::ON);

        $ret = $this->loadFiles($this->directory);
        usort(
            $ret,
            function (stdClass $a, stdClass $b) use ($c): int {
                $aName = trim(preg_replace('/[^0-9A-Za-z]+/', ' ', $a->name));
                $aName2 = ltrim($aName, '@');
                $bName = trim(preg_replace('/[^0-9A-Za-z]+/', ' ', $b->name));
                $bName2 = ltrim($bName, '@');
                return $c->compare($aName2, $bName2) ?: $c->compare($aName, $bName) ?: 0;
            },
        );
        return $ret;
    }

    /**
     * @return stdClass[]
     */
    private function loadFiles(string $directory): array
    {
        $basedir = TypeHelper::shouldBeString(Yii::getAlias($directory));
        $ret = [];
        /** @var iterable<RecursiveDirectoryIterator> $it */
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basedir),
        );
        foreach ($it as $entry) {
            if (!$entry->isFile()) {
                continue;
            }

            $pathname = $entry->getPathname();
            if (substr($pathname, 0, strlen($basedir)) !== $basedir) {
                continue;
            }

            if (substr($pathname, -12) !== '-LICENSE.txt') {
                continue;
            }

            $basename = substr($pathname, strlen($basedir));
            $html = $this->loadPlain(
                $entry->getPathname(),
                fn (string $t): bool => (bool)TypeHelper::shouldBeInteger(
                    preg_match('/copyright|licen[cs]e/i', $t),
                ),
            );
            if ($html) {
                $ret[] = (object)[
                    'name' => ltrim(substr($basename, 0, strlen($basename) - 12), '/'),
                    'html' => $html,
                ];
            }
        }

        return $ret;
    }

    private function loadPlain(string $path, ?callable $checker = null): ?string
    {
        $text = $this->loadFile($path, $checker);
        return $text !== null
            ? Html::tag('pre', Html::encode($text), ['class' => 'm-0 fs-6 lh-sm'])
            : null;
    }

    private function loadFile(string $path, ?callable $checker): ?string
    {
        $text = TypeHelper::shouldBeString(file_get_contents($path, false));
        if ($checker && !call_user_func($checker, $text)) {
            return null;
        }
        return $text;
    }
}
