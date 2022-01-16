<?php

declare(strict_types=1);

namespace app\commands;

use Exception;
use Yii;
use app\helpers\TypeHelper;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Json;

final class BootstrapIconsController extends Controller
{
    private const JSON_PATH = '@app/vendor/twbs/bootstrap-icons/font/bootstrap-icons.json';

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
