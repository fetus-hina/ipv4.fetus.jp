<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use yii\i18n\GettextMessageSource;

return [
    'translations' => [
        'app*' => [
            'class' => GettextMessageSource::class,
            'basePath' => '@app/messages',
            'catalog' => 'messages', // basename
            'sourceLanguage' => 'en-US',
        ],
    ],
];
