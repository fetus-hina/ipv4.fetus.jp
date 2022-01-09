<?php

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
