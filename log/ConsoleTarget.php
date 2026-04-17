<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\log;

use Throwable;
use yii\helpers\VarDumper;
use yii\log\FileTarget;

use function is_string;

final class ConsoleTarget extends FileTarget
{
    /** @return void */
    public function init()
    {
        if ($this->logFile === null) {
            $this->logFile = 'php://stderr';
        }
        $this->enableRotation = false;
        parent::init();
    }

    /**
     * @param array{0: mixed, 1: int, 2: string, 3: float, 4?: mixed, 5?: mixed} $message
     * @return string
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
    public function formatMessage($message)
    {
        [$text, , , $timestamp] = $message;
        if (!is_string($text)) {
            $text = $text instanceof Throwable
                ? (string)$text
                : VarDumper::export($text);
        }
        return $this->getTime($timestamp) . ' ' . $text;
    }
}
