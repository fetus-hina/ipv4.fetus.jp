<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

echo implode('<br>', [
    Yii::t('app', 'Between "{start}" and "{end}" is a comment.', [
        'start' => '<code>&lt;!--</code>',
        'end' => '<code>--&gt;</code>',
    ]),
    Yii::t('app', 'The lines of the comment begins with "{hash}" so that it can be easily removed by line-based processors (e.g., {grep}).', [
        'hash' => '<code>#</code>',
        'grep' => '<code>grep</code>',
    ]),
]);
