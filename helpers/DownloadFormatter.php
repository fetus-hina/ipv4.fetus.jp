<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\helpers;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Generator;
use Yii;
use app\models\DownloadTemplate;
use app\models\Newline;
use yii\helpers\Url;

use function array_merge;
use function assert;
use function chr;
use function count;
use function explode;
use function htmlspecialchars;
use function implode;
use function ip2long;
use function is_array;
use function is_int;
use function is_string;
use function long2ip;
use function ltrim;
use function preg_match;
use function preg_replace_callback;
use function str_contains;
use function str_repeat;
use function str_replace;
use function strlen;
use function strtolower;
use function strtoupper;
use function substr;
use function time;
use function trim;
use function vsprintf;

use const ENT_QUOTES;
use const ENT_XML1;

final class DownloadFormatter
{
    /**
     * @param string|array $thisUrl このリスト自体のURL
     * @param string|array $pageUrl このリストを提供するページのURL
     */
    public static function format(
        string $name, // e.g., "[jp] 日本 (Japan)"
        string $cc, // "jp" or "krfilter_1"
        string|array $thisUrl,
        string|array $pageUrl,
        DownloadTemplate $template,
        bool $isAllow,
        iterable $cidrList,
        ?string $note,
        bool $outputHeaders = true,
    ): Generator {
        $newline = static::getNewLineCode($template->newline);

        if ($template->file_begin !== null && $template->file_begin !== '') {
            yield static::fillPlaceholder($template->file_begin, $template, $cc, null, $isAllow) . $newline;
            yield $newline;
        }

        if ($outputHeaders) {
            foreach (static::generateHeaders($name, $thisUrl, $pageUrl, $template, $note) as $row) {
                yield $row . $newline;
            }

            yield $newline;
        }

        if ($template->list_begin !== null && $template->list_begin !== '') {
            yield static::fillPlaceholder($template->list_begin, $template, $cc, null, $isAllow) . $newline;
        }

        if ($template->line_limit === null) {
            foreach ($cidrList as $cidr) {
                yield static::fillPlaceholder($template->template, $template, $cc, $cidr, $isAllow) . $newline;
            }
        } else {
            $currentCidrs = [];
            foreach ($cidrList as $cidr) {
                $testLine = self::fillPlaceholder(
                    $template->template,
                    $template,
                    $cc,
                    array_merge($currentCidrs, [$cidr]),
                    $isAllow,
                );

                if (strlen($testLine) > $template->line_limit) {
                    // オーバーフローしたので、直前のCIDRまでで1行を作成する
                    yield static::fillPlaceholder(
                        $template->template,
                        $template,
                        $cc,
                        $currentCidrs,
                        $isAllow,
                    ) . $newline;

                    // 現在のCIDRを次の行の先頭にする
                    $currentCidrs = [$cidr];
                } else {
                    // まだオーバーフローしていないので、現在のCIDRを追加する
                    $currentCidrs[] = $cidr;
                }
            }

            // 最後の行を出力する
            if (count($currentCidrs)) {
                yield static::fillPlaceholder(
                    $template->template,
                    $template,
                    $cc,
                    $currentCidrs,
                    $isAllow,
                ) . $newline;
            }
        }

        if ($template->list_end !== null && $template->list_end !== '') {
            yield static::fillPlaceholder($template->list_end, $template, $cc, null, $isAllow) . $newline;
        }

        if ($template->file_end !== null && $template->file_end !== '') {
            yield static::fillPlaceholder($template->file_end, $template, $cc, null, $isAllow) . $newline;
        }
    }

    /**
     * @param string|string[]|null $cidr
     */
    private static function fillPlaceholder(
        string $text,
        DownloadTemplate $template,
        string $cc,
        string|array|null $cidr,
        bool $isAllow,
    ): string {
        return TypeHelper::shouldBeString(
            preg_replace_callback(
                '/\{([a-zA-Z0-9_]+)((?::[A-Za-z0-9]+)+)?\}/',
                function (array $match) use ($template, $cc, $cidr, $isAllow): string {
                    switch ($match[1]) {
                        case 'broadcast':
                            if (!is_string($cidr)) {
                                throw new Exception('Unexpected {broadcast} in download template');
                            }
                            return static::formatPlaceholder(
                                static::calcBroadcastAddress($cidr),
                                explode(':', ltrim($match[2] ?? '', ':')),
                            );

                        case 'cc':
                            return static::formatPlaceholder($cc, explode(':', ltrim($match[2] ?? '', ':')));

                        case 'cidr':
                            if (!is_string($cidr)) {
                                throw new Exception('Unexpected {cidr} in download template');
                            }
                            return static::formatPlaceholder($cidr, explode(':', ltrim($match[2] ?? '', ':')));

                        case 'cidr_list':
                            if ($cidr === null || !is_array($cidr)) {
                                throw new Exception('Unexpected {cidr_list} in download template');
                            }
                            return static::formatPlaceholder(
                                implode(' ', $cidr),
                                explode(':', ltrim($match[2] ?? '', ':')),
                            );

                        case 'control':
                            if ($template->allow === null || $template->deny === null) {
                                throw new Exception('Unexpected {control} in download template');
                            }
                            return static::formatPlaceholder(
                                $isAllow ? $template->allow : $template->deny,
                                explode(':', ltrim($match[2] ?? '', ':')),
                            );

                        case 'control_not':
                            if ($template->allow === null || $template->deny === null) {
                                throw new Exception('Unexpected {control_not} in download template');
                            }
                            return static::formatPlaceholder(
                                !$isAllow ? $template->allow : $template->deny,
                                explode(':', ltrim($match[2] ?? '', ':')),
                            );

                        case 'network':
                            if (!is_string($cidr)) {
                                throw new Exception('Unexpected {network} in download template');
                            }
                            return static::formatPlaceholder(
                                explode('/', $cidr)[0],
                                explode(':', ltrim($match[2] ?? '', ':')),
                            );

                        case 'prefix':
                            if (!is_string($cidr)) {
                                throw new Exception('Unexpected {prefix} in download template');
                            }
                            return static::formatPlaceholder(
                                (string)(int)explode('/', $cidr)[1],
                                explode(':', ltrim($match[2] ?? '', ':')),
                            );

                        case 'subnet':
                            if (!is_string($cidr)) {
                                throw new Exception('Unexpected {subnet} in download template');
                            }
                            return static::formatPlaceholder(
                                static::prefixToSubnetMask((int)explode('/', $cidr)[1]),
                                explode(':', ltrim($match[2] ?? '', ':')),
                            );

                        case 'count':
                            if (!is_string($cidr)) {
                                throw new Exception('Unexpected {count} in download template');
                            }
                            return static::formatPlaceholder(
                                (string)self::prefixToAddressCount((int)explode('/', $cidr)[1]),
                                explode(':', ltrim($match[2] ?? '', ':')),
                            );
                    }
                    return $match[0];
                },
                $text,
            ),
        );
    }

    private static function prefixToSubnetMask(int $prefix): string
    {
        if ($prefix < 1 || $prefix > 32) {
            throw new Exception('Invalid prefix: ' . $prefix);
        }

        // phpcs:ignore SlevomatCodingStandard.PHP.UselessParentheses.UselessParentheses
        $maskBin = (0xffffffff << (32 - $prefix)) & 0xffffffff;
        return TypeHelper::shouldBeString(long2ip($maskBin));
    }

    private static function calcBroadcastAddress(string $cidr): string
    {
        if (!preg_match('#^([0-9.]+)/([0-9]+)$#', $cidr, $match)) {
            throw new Exception('Invalid CIDR: ' . $cidr);
        }

        $network = @ip2long($match[1]);
        if (!is_int($network)) {
            throw new Exception('ip2long failed');
        }

        $prefix = (int)$match[2];
        if ($prefix < 1 || $prefix > 32) {
            throw new Exception('Invalid prefix: ' . $prefix);
        }

        // phpcs:ignore SlevomatCodingStandard.PHP.UselessParentheses.UselessParentheses
        $subnetMask = (0xffffffff << (32 - $prefix)) & 0xffffffff;
        $broadcast = $network | ($subnetMask ^ 0xffffffff);
        return TypeHelper::shouldBeString(long2ip($broadcast));
    }

    private static function prefixToAddressCount(int $prefix): int
    {
        if ($prefix < 1 || $prefix > 32) {
            throw new Exception('Invalid prefix: ' . $prefix);
        }

        return (int)(2 ** (32 - $prefix));
    }

    private static function formatPlaceholder(string $value, array $modifiers): string
    {
        foreach ($modifiers as $modifier) {
            $value = static::applyModifier($value, $modifier);
        }
        return $value;
    }

    private static function applyModifier(string $value, string $modifier): string
    {
        switch ((string)trim($modifier)) {
            case '':
                return $value;

            case 'csv':
                if (
                    !str_contains($value, ',') &&
                    !str_contains($value, '"') &&
                    !str_contains($value, "\n") &&
                    !str_contains($value, "\r")
                ) {
                    return $value;
                }
                return '"' . str_replace('"', '""', $value) . '"';

            case 'fillSpace':
                $len = strlen('000.000.000.000/32');
                return substr($value . str_repeat(' ', $len), 0, $len);

            case 'lower':
                return strtolower($value);

            case 'upper':
                return strtoupper($value);

            case 'xml':
                return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
        }

        throw new Exception('Unknown modifier: ' . $modifier);
    }

    private static function generateHeaders(
        string $name,
        string|array $thisUrl,
        string|array $pageUrl,
        DownloadTemplate $template,
        ?string $note,
    ): Generator {
        $comment = $template->commentStyle;
        assert($comment !== null);

        $row = fn (string $text): string => implode('', [
            $comment->line_begin !== null && $comment->line_begin !== ''
                    ? $comment->line_begin . ' '
                    : '',
            $text,
            $comment->line_end !== null && $comment->line_end !== ''
                    ? $comment->line_end . ' '
                    : '',
        ]);

        if ($comment->block_begin !== null && $comment->block_begin !== '') {
            yield $comment->block_begin;
        }

        $time = (new DateTimeImmutable())
            // phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
            ->setTimestamp((int)($_SERVER['REQUEST_TIME'] ?? time()))
            ->setTimezone(new DateTimeZone(Yii::$app->timeZone));

        yield $row('');
        yield $row($name);
        yield $row(' ' . Url::to($pageUrl, true));
        yield $row('');
        yield $row(Url::to($thisUrl, true));
        yield $row(vsprintf(' 出力日時(Output): %s (%s)', [
            $time->format('Y-m-d H:i:s T'),
            $time->setTimeZone(new DateTimeZone('Etc/UTC'))->format(DateTime::ATOM),
        ]));

        if ($note !== null) {
            yield $row('');
            foreach (explode("\n", $note) as $item) {
                yield $row($item);
            }
        }

        if ($template->usage !== null && $template->usage !== '') {
            yield $row('');
            yield $row('Usage:');
            foreach (explode("\n", $template->usage) as $item) {
                yield $row(' ' . $item);
            }
        }

        yield $row('');
        yield $row('自動化したアクセスについて (About automated access):');
        yield $row(' ' . Url::to(['site/about', '#' => 'automation'], true));
        yield $row('');

        if ($comment->block_end !== null && $comment->block_end !== '') {
            yield $comment->block_end;
        }
    }

    private static function getNewLineCode(?Newline $newline): string
    {
        return match ($newline?->key) {
            'unix' => chr(0x0a),
            'win' => chr(0x0d) . chr(0x0a),
            default => "\n",
        };
    }
}
