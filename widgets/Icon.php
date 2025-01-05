<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\widgets;

use Yii;
use app\assets\BootstrapIconsAsset;
use yii\base\UnknownMethodException;
use yii\helpers\Html;
use yii\web\AssetBundle;
use yii\web\View;

/**
 * @method static string checkboxChecked()
 * @method static string checkboxUnchecked()
 * @method static string close()
 * @method static string download()
 * @method static string github()
 * @method static string linkBack()
 * @method static string linkNext()
 * @method static string pagerFirst()
 * @method static string pagerLast()
 * @method static string pagerNext()
 * @method static string pagerPrev()
 * @method static string popupModal()
 * @method static string radioChecked()
 * @method static string radioUnchecked()
 * @method static string search()
 * @method static string sortAsc()
 * @method static string sortDesc()
 * @method static string translate()
 * @method static string twitter()
 */
final class Icon
{
    /**
     * @var array<string, string>
     */
    private static array $biMap = [
        'checkboxChecked' => 'bi-check2-square',
        'checkboxUnchecked' => 'bi-square',
        'close' => 'bi-x-lg',
        'download' => 'bi-download',
        'github' => 'bi-github',
        'linkBack' => 'bi-chevron-left',
        'linkNext' => 'bi-chevron-right',
        'pagerFirst' => 'bi-chevron-double-left',
        'pagerLast' => 'bi-chevron-double-right',
        'pagerNext' => 'bi-chevron-right',
        'pagerPrev' => 'bi-chevron-left',
        'popupModal' => 'bi-window-stack',
        'radioChecked' => 'bi-record-circle-fill',
        'radioUnchecked' => 'bi-circle',
        'search' => 'bi-search',
        'sortAsc' => 'bi-arrow-down-short',
        'sortDesc' => 'bi-arrow-up-short',
        'translate' => 'bi-translate',
        'twitter' => 'bi-twitter-x',
    ];

    public static function __callStatic(string $name, array $args): string
    {
        return match (true) {
            isset(self::$biMap[$name]) => self::bi(self::$biMap[$name]),
            default => throw new UnknownMethodException("Unknown icon {$name}"),
        };
    }

    private static function bi(string $name): string
    {
        self::prepareAsset(BootstrapIconsAsset::class);

        return Html::tag('span', '', [
            'aria' => ['hidden' => 'true'],
            'class' => ['bi', $name],
        ]);
    }

    /**
     * @phpstan-param class-string<AssetBundle> $fqcn
     */
    private static function prepareAsset(string $fqcn): void
    {
        static $registered = [];
        if (isset($registered[$fqcn])) {
            return;
        }

        // @phpstan-ignore-next-line nullsafe.neverNull
        $view = Yii::$app?->view ?? null;
        if ($view instanceof View) {
            $view->registerAssetBundle($fqcn);
            $registered[$fqcn] = true;
        }
    }
}
