<?php

declare(strict_types=1);

namespace app\helpers;

use DeviceDetector\ClientHints as MatomoCH;
use DeviceDetector\DeviceDetector;
use Throwable;
use Yii;
use app\models\Language;
use yii\base\Application as BaseApplication;
use yii\base\BootstrapInterface;
use yii\helpers\ArrayHelper;
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

    /**
     * @return array<string, string>
     */
    public static function getValidLanguages(): array
    {
        return \array_map(
            fn (Language $model): string => $model->native_name,
            self::getValidLanguagesEx(),
        );
    }

    /**
     * @return array<string, Language>
     */
    public static function getValidLanguagesEx(): array
    {
        static $cache = null;
        if ($cache === null) {
            $cache = ArrayHelper::map(
                Language::find()->orderBy(['sort' => SORT_ASC, 'id' => SORT_ASC])->all(),
                'id',
                fn (Language $model): Language => $model,
            );
        }
        return $cache;
    }

    public static function isValidLanguageCode(string $code): bool
    {
        return Language::find()
            ->andWhere(['id' => $code])
            ->exists();
    }

    public static function isLatin(string $code): bool
    {
        return self::isMatchCharacterCategory($code, 'latin');
    }

    public static function isJapanese(string $code): bool
    {
        return self::isMatchCharacterCategory($code, 'japanese');
    }

    private static function isMatchCharacterCategory(string $code, string $categoryKey): bool
    {
        return Language::findOne(['id' => $code])
            ?->character
            ?->key === $categoryKey;
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

        foreach (self::getValidLanguagesEx() as $lang) {
            $view->registerLinkTag([
                'href' => Url::to(
                    \array_merge($url, [
                        self::URL_PARAM => $lang->id,
                    ]),
                    true,
                ),
                'hreflang' => $lang->hreflang ?: $lang->id,
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
                ?: $this->getDefaultLanguage($app)
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

    private function vary(Application $app, string $value): void
    {
        $app->response->headers->add('Vary', $value);
    }

    private function getDefaultLanguage(Application $app): ?string
    {
        $model = Language::find()
            ->andWhere(['is_default' => true])
            ->orderBy(['sort' => SORT_ASC])
            ->limit(1)
            ->one();
        return $model?->id;
    }
}
