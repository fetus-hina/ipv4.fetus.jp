<?php

declare(strict_types=1);

echo implode('<br>', [
    Yii::t('app/schema', 'All lines beginning with "{code}" are comments.', [
        'code' => '<code>#</code>',
    ]),
    Yii::t('app/schema', 'The content is not specified and is assumed to be read by humans.'),
    Yii::t('app/schema', 'For machine processing, this line should be ignored.'),
    Yii::t('app/schema', 'Never start a comment in the middle of a line.'),
    Yii::t('app/schema', 'They may also be placed between data records.'),
]);
