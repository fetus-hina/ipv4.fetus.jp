<?php

declare(strict_types=1);

namespace app\i18n;

use DateTimeInterface;
use IntlDatePatternGenerator;
use LogicException;
use Yii;
use app\helpers\TypeHelper;
use app\helpers\Unicode;
use yii\i18n\Formatter as YiiFormatter;

use function preg_replace;
use function strtolower;
use function trim;

final class Formatter extends YiiFormatter
{
    private const DATE_FORMAT_LONG = 'long';
    private const DATE_FORMAT_SHORT = 'short';

    /**
     * @var string|null
     */
    public $nullDisplay = '';

    public function asLongDate(
        int|string|DateTimeInterface|null $value,
    ): string {
        return $this->asDate(
            $value,
            $this->dateFormat(self::DATE_FORMAT_LONG),
        );
    }

    public function asShortDate(
        int|string|DateTimeInterface|null $value,
    ): string {
        return $this->asDate(
            $value,
            $this->dateFormat(self::DATE_FORMAT_SHORT),
        );
    }

    /**
     * @param self::DATE_FORMAT_* $type
     */
    private function dateFormat(string $type): string
    {
        $locale = trim(Yii::$app->language);
        $lang = strtolower(
            TypeHelper::shouldBeString(preg_replace('/^([a-z]+).*/i', '$1', $locale)),
        );

        if ($lang !== 'ja' && $lang !== 'en') {
            throw new LogicException("Unsupported locale: $locale");
        }

        return match ($type) {
            self::DATE_FORMAT_LONG => match ($lang) {
                'ja' => 'medium',
                'en' => 'long',
            },
            self::DATE_FORMAT_SHORT => match ($lang) {
                'ja' => 'short',
                'en' => self::shortDatePattern('en-US'),
            },
        };
    }

    private static function shortDatePattern(string $locale): string
    {
        $gen = new IntlDatePatternGenerator($locale);
        return TypeHelper::shouldBeString($gen->getBestPattern('yyyy MMM dd'));
    }

    public function asRegionalIndicator(?string $value): string
    {
        return $value === null
            ? (string)$this->nullDisplay
            : Unicode::asciiToRegionalIndicator($value);
    }
}
