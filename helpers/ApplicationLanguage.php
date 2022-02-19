<?php

declare(strict_types=1);

namespace app\helpers;

use DeviceDetector\DeviceDetector;
use Throwable;
use Yii;
use yii\base\Application as BaseApplication;
use yii\base\BootstrapInterface;
use yii\helpers\Url;
use yii\web\Application;
use yii\web\View;

final class ApplicationLanguage implements BootstrapInterface
{
    public const URL_PARAM = '_lang';

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

    public static function registerLink(BaseApplication $app, array $url): void
    {
        if (!$app instanceof Application) {
            return;
        }

        if (!($view = $app->view) instanceof View) {
            return;
        }

        unset($url[self::URL_PARAM]);

        $view->registerLinkTag([
            'href' => Url::to($url, true),
            'hreflang' => 'x-default',
            'rel' => 'canonical',
            'type' => 'text/html',
        ]);
        foreach (array_keys(self::getValidLanguages()) as $lang) {
            $view->registerLinkTag([
                'href' => Url::to(
                    array_merge($url, [
                        self::URL_PARAM => $lang,
                    ]),
                    true,
                ),
                'hreflang' => self::removeGenericCountry($lang),
                'rel' => 'alternate',
                'type' => 'text/html',
            ]);
        }
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
        return $this->detectLanguageFromUrl($app)
            ?: $this->returnJapaneseIfRobot($app)
            ?: $this->detectLanguageFromCookie($app)
            ?: $this->detectLanguageFromBrowser($app)
            ?: self::LANGUAGE_ENGLISH;
    }

    private function detectLanguageFromUrl(Application $app): ?string
    {
        $value = $app->request->get(self::URL_PARAM);
        return is_string($value) && self::isValidLanguageCode($value)
            ? $value
            : null;
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
                $this->vary($app, 'User-Agent');
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
        if (is_string($cookie) && self::isValidLanguageCode($cookie)) {
            $this->vary($app, 'Cookie');
            return $cookie;
        }

        return null;
    }

    private function detectLanguageFromBrowser(Application $app): string
    {
        $this->vary($app, 'Accept-Language');
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

    private function vary(Application $app, string $value): void
    {
        $app->response->headers->add('Vary', $value);
    }

    private static function removeGenericCountry(string $code): string
    {
        if (preg_match('/^([a-z]+)-([a-z0-9]+)/i', $code, $match)) {
            // 言語コードと国コードが同じ
            if (strtolower($match[1]) === strtolower($match[2])) {
                return $match[1];
            }

            // 特定の組み合わせだと generic だとみなす
            $mainCountry = match (strtolower($match[1])) {
                'en' => 'us',
                'ja' => 'jp',
                default => null,
            };
            if (strtolower($match[2]) === $mainCountry) {
                return $match[1];
            }
        }

        return $code;
    }
}
