<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\helpers;

use TypeError;
use yii\base\InvalidArgumentException;
use yii\db\Connection as DbConnection;
use yii\helpers\ArrayHelper;

use function get_resource_type;
use function gettype;
use function is_array;
use function is_int;
use function is_object;
use function is_resource;
use function is_string;
use function vsprintf;

final class TypeHelper
{
    public const ARRAY_DONTCARE = 0;
    public const ARRAY_INDEXED = 1;
    public const ARRAY_INT_KEYS = 2;
    public const ARRAY_ASSOC = 3;

    /**
     * @param self::ARRAY_DONTCARE|self::ARRAY_INDEXED|self::ARRAY_INT_KEYS|self::ARRAY_ASSOC $type
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
    public static function shouldBeArray(mixed $value, int $type = self::ARRAY_DONTCARE): array
    {
        if (!is_array($value)) {
            throw self::error('array', $value);
        }

        switch ($type) {
            case self::ARRAY_DONTCARE:
                break;

            case self::ARRAY_INDEXED:
                if (!ArrayHelper::isIndexed($value, true)) {
                    throw self::error('indexed array (consecutive)', $value, false);
                }
                break;

            case self::ARRAY_INT_KEYS:
                if (!ArrayHelper::isIndexed($value, false)) {
                    throw self::error('indexed array', $value, false);
                }
                break;

            case self::ARRAY_ASSOC:
                if (!ArrayHelper::isAssociative($value, false)) {
                    throw self::error('associative array', $value, false);
                }
                break;

            default:
                throw new InvalidArgumentException();
        }

        return $value;
    }

    public static function shouldBeDb(mixed $value): DbConnection
    {
        return self::shouldBeInstanceOf($value, DbConnection::class);
    }

    /**
     * @template T of object
     * @param class-string<T> $fqcn
     * @return T
     */
    public static function instanceOf(mixed $value, string $fqcn): object
    {
        return self::shouldBeInstanceOf($value, $fqcn);
    }

    /**
     * @template T of object
     * @param class-string<T> $fqcn
     * @return T
     */
    public static function shouldBeInstanceOf(mixed $value, string $fqcn): object
    {
        return is_object($value) && ($value instanceof $fqcn)
            ? $value
            : throw self::error($fqcn, $value);
    }

    public static function shouldBeInteger(mixed $value): int
    {
        return is_int($value) ? $value : throw self::error('integer', $value);
    }

    public static function string(mixed $value): string
    {
        return self::shouldBeString($value);
    }

    public static function shouldBeString(mixed $value): string
    {
        return is_string($value) ? $value : throw self::error('string', $value);
    }

    private static function error(string $typeName, mixed $value, bool $putActualValue = true): never
    {
        throw new TypeError(
            $putActualValue
                ? vsprintf('Type Error: argument type should be "%s", but it is "%s"', [
                    $typeName,
                    self::getType($value),
                ])
                : vsprintf('Type Error: argument type should be "%s"', [
                    $typeName,
                ]),
        );
    }

    private static function getType(mixed $value): string
    {
        switch (true) {
            case is_object($value):
                return $value::class;

            case is_resource($value):
                return get_resource_type($value);
        }

        return gettype($value);
    }
}
