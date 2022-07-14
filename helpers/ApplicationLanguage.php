<?php

declare(strict_types=1);

namespace app\helpers;

use DeviceDetector\ClientHints as MatomoCH;
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

    private const DEVICE_DETECTOR_CACHE_DURATION = 604800;

    public static function getValidLanguages(): array
    {
        static $list = null;
        if ($list === null) {
            $list = [
                self::LANGUAGE_ENGLISH => 'English',
                self::LANGUAGE_JAPANESE => '日本語',
            ];

            \uksort(
                $list,
                fn (string $a, string $b): int => self::getPrecedence($a) <=> self::getPrecedence($b)
                    ?: \strcmp($list[$a], $list[$b])
                    ?: \strcmp($a, $b),
            );
        }

        return $list;
    }

    public static function isValidLanguageCode(string $code): bool
    {
        return \in_array(
            $code,
            \array_keys(self::getValidLanguages()),
            true,
        );
    }

    public static function isLatin(string $code): bool
    {
        return $code === self::LANGUAGE_ENGLISH;
    }

    public static function isJapanese(string $code): bool
    {
        return $code === self::LANGUAGE_JAPANESE;
    }

    public static function isAutoDetect(?Application $app = null): bool
    {
        $request = ($app ?? Yii::$app)->request;
        $cookie = $request->cookies->getValue(self::COOKIE_NAME);
        return !\is_string($cookie) || !self::isValidLanguageCode($cookie);
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
        foreach (\array_keys(self::getValidLanguages()) as $lang) {
            $view->registerLinkTag([
                'href' => Url::to(
                    \array_merge($url, [
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
        $profiler = new Profiler(
            'Detect user language',
            __METHOD__,
        );
        try {
            return $this->detectLanguageFromUrl($app)
                ?: $this->returnJapaneseIfRobot($app)
                ?: $this->detectLanguageFromCookie($app)
                ?: $this->detectLanguageFromBrowser($app)
                ?: self::LANGUAGE_ENGLISH;
        } finally {
            unset($profiler);
        }
    }

    private function detectLanguageFromUrl(Application $app): ?string
    {
        $value = $app->request->get(self::URL_PARAM);
        return \is_string($value) && self::isValidLanguageCode($value)
            ? $value
            : null;
    }

    private function returnJapaneseIfRobot(Application $app): ?string
    {
        $profiler = new Profiler('Run "returnJapaneseIfRobot"', __METHOD__);
        try {
            $ua = $app->request->userAgent;
            if (
                !\is_string($ua) ||
                $ua === '' ||
                !\mb_check_encoding($ua, 'UTF-8')
            ) {
                return null;
            }

            if ($this->isBot($ua)) {
                $this->vary($app, 'User-Agent');
                return self::LANGUAGE_JAPANESE;
            }

            return null;
        } finally {
            unset($profiler);
        }
    }

    /**
     * @param non-empty-string $userAgent
     */
    private function isBot(string $userAgent): bool
    {
        $ch = ClientHints::factory();
        $cacheId = \vsprintf('%s(%s)', [
            __METHOD__,
            \hash_hmac(
                'sha256',
                $userAgent,
                ClientHints::getCacheId($ch),
            ),
        ]);

        $profiler = new Profiler("{$cacheId}: {$userAgent}", __METHOD__);
        try {
            $value = Yii::$app->cache->get($cacheId);
            if ($value !== false) {
                return $value === 1;
            }

            $isBot = $this->isBotByDeviceDetector($userAgent, $ch);
            Yii::$app->cache->set(
                $cacheId,
                $isBot ? 1 : 0,
                self::DEVICE_DETECTOR_CACHE_DURATION,
            );
            return $isBot;
        } finally {
            unset($profiler);
        }
    }

    /**
     * @param non-empty-string $userAgent
     */
    private function isBotByDeviceDetector(string $userAgent, MatomoCH $ch): bool
    {
        $profiler = new Profiler('Run device detector', __METHOD__);
        try {
            $dd = new DeviceDetector($userAgent, $ch);
            $dd->parse();
            return $dd->isBot();
        } catch (Throwable $e) {
            if (YII_ENV_DEV) {
                throw $e;
            }
            return false;
        } finally {
            unset($profiler);
        }
    }

    private function detectLanguageFromCookie(Application $app): ?string
    {
        $request = $app->request;
        $cookie = $request->cookies->getValue(self::COOKIE_NAME);
        if (\is_string($cookie) && self::isValidLanguageCode($cookie)) {
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
            \array_keys($this->getValidLanguages())
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
        if (\preg_match('/^([a-z]+)-([a-z0-9]+)/i', $code, $match)) {
            // 言語コードと国コードが同じ
            if (\strtolower($match[1]) === \strtolower($match[2])) {
                return $match[1];
            }

            // 特定の組み合わせだと generic だとみなす
            $mainCountry = match (\strtolower($match[1])) {
                'en' => 'us',
                'ja' => 'jp',
                default => null,
            };
            if (\strtolower($match[2]) === $mainCountry) {
                return $match[1];
            }
        }

        return $code;
    }
}
