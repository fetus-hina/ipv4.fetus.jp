<?php

declare(strict_types=1);

namespace app\i18n;

use DateTimeInterface;
use LogicException;
use Yii;
use app\helpers\TypeHelper;
use app\helpers\Unicode;

final class Formatter extends \yii\i18n\Formatter
{
    private const DATE_FORMAT_LONG = 'long';
    private const DATE_FORMAT_SHORT = 'short';

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

    private function dateFormat(string $type): string
    {
        $locale = \trim(Yii::$app->language);
        $lang = \strtolower(
            TypeHelper::shouldBeString(\preg_replace('/^([a-z]+).*/i', '$1', $locale))
        );

        return match ($type) {
            self::DATE_FORMAT_LONG => match ($lang) {
                'ja' => 'medium',
                default => 'long',
            },
            self::DATE_FORMAT_SHORT => match ($lang) {
                'ja' => 'short',
                default => 'php:Y-M-d',
            },
            default => throw new LogicException(),
        };
    }

    public function asRegionalIndicator(?string $value): string
    {
        return $value === null
            ? $this->nullDisplay
            : Unicode::asciiToRegionalIndicator($value);
    }
}
