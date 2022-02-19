<?php

declare(strict_types=1);

namespace app\helpers;

use DeviceDetector\DeviceDetector;
use Throwable;
use Yii;
use yii\base\BootstrapInterface;
use yii\web\Application;

final class ApplicationLanguage implements BootstrapInterface
{
    public const COOKIE_NAME = 'language';

    public const LANGUAGE_ENGLISH = 'en-US';
    public const LANGUAGE_JAPANESE = 'ja-JP';

    public static function getValidLanguages(): array
    {
        static $list = null;
        if ($list === null) {
            $list = [
                self::LANGUAGE_ENGLISH => 'English',
                self::LANGUAGE_JAPANESE => '日本語',
            ];

            uksort(
                $list,
                fn (string $a, string $b): int => self::getPrecedence($a) <=> self::getPrecedence($b)
                    ?: strcmp($list[$a], $list[$b])
                    ?: strcmp($a, $b),
            );
        }

        return $list;
    }

    public static function isValidLanguageCode(string $code): bool
    {
        return in_array(
            $code,
            array_keys(self::getValidLanguages()),
            true,
        );
    }

    public static function isAutoDetect(?Application $app = null): bool
    {
        $request = ($app ?? Yii::$app)->request;
        $cookie = $request->cookies->getValue(self::COOKIE_NAME);
        return !is_string($cookie) || !self::isValidLanguageCode($cookie);
    }

    /** @inheritdoc */
    public function bootstrap($app): void
    {
        if (!$app instanceof Application) {
            return;
        }

        $app->language = $this->detectLanguage($app);
    }

    private function detectLanguage(Application $app): string
    {
        return $this->returnJapaneseIfRobot($app)
            ?: $this->detectLanguageFromCookie($app)
            ?: $this->detectLanguageFromBrowser($app)
            ?: self::LANGUAGE_ENGLISH;
    }

    private function returnJapaneseIfRobot(Application $app): ?string
    {
        if (!$ua = $app->request->userAgent) {
            return null;
        }

        try {
            $dd = new DeviceDetector($ua);
            $dd->parse();
            if ($dd->isBot()) {
                return self::LANGUAGE_JAPANESE;
            }
        } catch (Throwable $e) {
            if (YII_ENV_DEV) {
                throw $e;
            }
        }

        return null;
    }

    private function detectLanguageFromCookie(Application $app): ?string
    {
        $request = $app->request;
        $cookie = $request->cookies->getValue(self::COOKIE_NAME);
        return is_string($cookie) && self::isValidLanguageCode($cookie)
            ? $cookie
            : null;
    }

    private function detectLanguageFromBrowser(Application $app): string
    {
        $request = $app->request;
        return $request->getPreferredLanguage(
            array_keys($this->getValidLanguages())
        );
    }

    private static function getPrecedence(string $lang): int
    {
        return match ($lang) {
            self::LANGUAGE_ENGLISH => 1, // 英語が最優先
            self::LANGUAGE_JAPANESE => 2, // 日本語が次点
            default => 3, // その他の言語が生えることがあったらまとめて「その他」
        };
    }
}
