<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\helpers;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Generator;
use Yii;
use app\models\MergedCidr;
use yii\helpers\Url;

use function rtrim;
use function strtoupper;
use function vsprintf;

use const SORT_ASC;

final class NginxGeoDumper
{
    /**
     * @return Generator<string>
     */
    public static function dump(bool $header = true): Generator
    {
        return self::proc(
            Url::to(['nginx-geo/index'], true),
            $header,
        );
    }

    /**
     * @return Generator<string>
     */
    private static function proc(string $url, bool $header): Generator
    {
        if ($header) {
            foreach (self::getHeaders($url) as $header) {
                yield rtrim('# ' . $header) . "\n";
            }
            yield "\n";
        }

        yield "geo \$ipv4cc {\n";
        yield "  default XX;\n";
        yield "\n";

        $cidrs = MergedCidr::find()->orderBy(['cidr' => SORT_ASC]);
        foreach ($cidrs->each(200) as $cidr) {
            yield vsprintf("  %-16s %s;\n", [
                TypeHelper::shouldBeInstanceOf($cidr, MergedCidr::class)->cidr,
                strtoupper(TypeHelper::shouldBeInstanceOf($cidr, MergedCidr::class)->region_id),
            ]);
        }

        yield "}\n";
    }

    /**
     * @return string[]
     */
    private static function getHeaders(string $url): array
    {
        $now = (new DateTimeImmutable('now', new DateTimeZone(Yii::$app->timeZone)));

        return [
            '',
            Url::to(['site/index'], true),
            '',
            $url,
            '',
            vsprintf('出力日時: %s (%s)', [
                $now->format('Y-m-d H:i:s T'),
                $now->setTimezone(new DateTimeZone('Etc/UTC'))->format(DateTimeInterface::ATOM),
            ]),
            '',
            '自動化したアクセスについて:',
            '  ' . Url::to(['site/about', '#' => 'automation'], true),
            '',
        ];
    }
}
