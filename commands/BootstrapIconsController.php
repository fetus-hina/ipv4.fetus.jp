<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use Exception;
use Yii;
use app\helpers\TypeHelper;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Json;

use function file_get_contents;
use function is_int;
use function ksort;
use function mb_chr;
use function vprintf;

use const SORT_FLAG_CASE;
use const SORT_NATURAL;

final class BootstrapIconsController extends Controller
{
    private const JSON_PATH = '@app/vendor/twbs/bootstrap-icons/font/bootstrap-icons.json';

    /**
     * @var string
     */
    public $defaultAction = 'convert';

    public function actionConvert(): int
    {
        $json = TypeHelper::shouldBeArray(
            Json::decode(
                TypeHelper::shouldBeString(
                    file_get_contents(
                        TypeHelper::shouldBeString(
                            Yii::getAlias(self::JSON_PATH),
                        ),
                    ),
                ),
            ),
            TypeHelper::ARRAY_ASSOC,
        );

        ksort($json, SORT_NATURAL | SORT_FLAG_CASE);

        echo "@charset 'UTF-8';\n";
        echo "\n";
        echo "/* This is an auto generated file (`yii bootstrap-icons/convert`). */\n";
        echo "\n";
        foreach ($json as $name => $codepoint) {
            $name = (string)$name;
            vprintf("\$bi-%s: '%s';\n", [
                (string)$name,
                is_int($codepoint)
                    ? mb_chr($codepoint, 'UTF-8')
                    : throw new Exception('Unexpected codepoint value for ' . $name),
            ]);
        }

        return ExitCode::OK;
    }
}
